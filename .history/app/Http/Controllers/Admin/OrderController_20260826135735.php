<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesCsv;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\OrderItem;
use App\Mail\OrderCancelled;
use App\Mail\OrderCancelledAdmin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use HandlesCsv;

    // The order export headers match the columns in the Excel screenshot exactly.
    private function orderExportHeaders(): array
    {
        // These headers are taken directly from the Excel screenshot you provided.
        return [
            'Order ID',
            'Invoice No',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Subtotal',
            'Discount',
            'Shipping Charge',
            'Grand Total',
            'Paid Amount',
            'Due Amount',
            'Order Date',
            'Order Time',
            'Product SKU',
            'Product Name',
            'Quantity',
            'Item Price',
            'Item Total'
        ];
    }

    private function buildOrderQuery(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('status')) {
            if ($request->status === 'returns') {
                $query->where(function ($q) {
                    $q->where('order_status', 'returns')
                        ->orWhereNotNull('return_requested_at');
                });
            } elseif ($request->status === 'cancelled') {
                $query->where(function ($q) {
                    $q->where('order_status', 'cancelled')
                        ->orWhereNotNull('cancel_requested_at');
                });
            } else {
                $query->where('order_status', $request->status);
            }
        }

        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'full':
                    $query->where('payment_status', 'paid')
                        ->where(function ($q) {
                            $q->whereNull('payment_type')
                                ->orWhere('payment_type', '!=', 'partial');
                        });
                    break;

                case 'partial_pending':
                    $query->where('payment_type', 'partial')
                        ->where('payment_status', 'partial');
                    break;

                case 'partial_completed':
                    $query->where('payment_type', 'partial')
                        ->where('payment_status', 'paid');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('request_status')) {
            switch ($request->request_status) {
                case 'cancel_pending':
                    $query->whereNotNull('cancel_requested_at')
                        ->whereNull('cancel_approved_at')
                        ->whereNull('cancel_rejected_at');
                    break;

                case 'return_pending':
                    $query->whereNotNull('return_requested_at')
                        ->whereNull('return_approved_at')
                        ->whereNull('return_rejected_at');
                    break;

                case 'pending':
                    $query->where(function ($q) {
                        $q->where(function ($cancelQuery) {
                            $cancelQuery->whereNotNull('cancel_requested_at')
                                ->whereNull('cancel_approved_at')
                                ->whereNull('cancel_rejected_at');
                        })->orWhere(function ($returnQuery) {
                            $returnQuery->whereNotNull('return_requested_at')
                                ->whereNull('return_approved_at')
                                ->whereNull('return_rejected_at');
                        });
                    });
                    break;
            }
        }

        return $query;
    }

    private function pendingRequestCount(): int
    {
        return Order::query()
            ->where(function ($q) {
                $q->where(function ($cancelQuery) {
                    $cancelQuery->whereNotNull('cancel_requested_at')
                        ->whereNull('cancel_approved_at')
                        ->whereNull('cancel_rejected_at');
                })->orWhere(function ($returnQuery) {
                    $returnQuery->whereNotNull('return_requested_at')
                        ->whereNull('return_approved_at')
                        ->whereNull('return_rejected_at');
                });
            })
            ->count();
    }

    /*
    |--------------------------------------------------
    | STORE ORDER (CLEAN + GST READY)
    |--------------------------------------------------
    */
    public function storeOrder(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $user = auth()->user();
    
            // 1. Cart Items
            $cartItems = \App\Models\Cart::with('product')
                ->where('user_id', $user->id)
                ->get();
    
            if ($cartItems->isEmpty()) {
                DB::rollBack();
    
                return back()->with('error', 'Cart is empty');
            }
    
            // 2. Subtotal
            $subtotal = 0;
    
            foreach ($cartItems as $item) {
                $subtotal += (float) $item->product->selling_price * $item->quantity;
            }
    
            // 3. Discount & Shipping
            $discount = (float) ($request->discount ?? 0);
            $shipping = (float) ($request->shipping_charge ?? 0);

            // 4. GST Rate
            $gstRate = (float) ($request->gst_rate ?? 18);

            if ($gstRate <= 0) {
                $gstRate = 18;
            }

            // 5. Amount after discount
            $netAmount = max(0, $subtotal - $discount);

            // 6. GST Inclusive
            $gstAmount = 0;

            if ($gstRate > 0) {
                $gstAmount = ($netAmount * $gstRate) / (100 + $gstRate);
            }

            // 7. Grand Total
            $grandTotal = $netAmount + $shipping;
    
            // 8. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'order_id' => 'ORD-' . date('Ymd') . rand(1000, 9999),
    
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_charge' => $shipping,
    
                // GST
                'gst_rate' => $gstRate,
                'gst_amount' => $gstAmount,
    
                'grand_total' => $grandTotal,
    
                'paid_amount' => 0,
                'due_amount' => $grandTotal,
    
                'payment_method' => $request->payment_method,
                'payment_type' => 'full',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);
    
            // 9. Order Items
            foreach ($cartItems as $item) {
    
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->selling_price,
                    'total' => (float) $item->product->selling_price * $item->quantity,
                ]);
            }
    
            // 10. Clear Cart
            \App\Models\Cart::where('user_id', $user->id)->delete();
    
            DB::commit();
    
            return redirect()
                ->route('order.success', $order->id)
                ->with('success', 'Order placed successfully');
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------
    | ORDER LIST
    |--------------------------------------------------
    */
    public function index(Request $request)
    {
        $orders = $this->buildOrderQuery($request)
            ->with(['user', 'addresses', 'items.product'])
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $pendingRequestCount = $this->pendingRequestCount();

        if ($request->ajax()) {
            return view('admin.order.partials.order-table', compact('orders', 'pendingRequestCount'))->render();
        }

        return view('admin.order.index', compact('orders', 'pendingRequestCount'));
    }

    /*
    |--------------------------------------------------
    | ORDER DETAILS
    |--------------------------------------------------
    */
    // public function show(Order $order)
    // {
    //     $order->load('user', 'items.product', 'orderPayments','addresses');

    //     return view('admin.order.show', compact('order'));
    // }

    public function show(Order $order)
    {
        $order->load('user','items.product','orderPayments','addresses'
        );
        // GST rate
        $gstRate = (float) $order->gst_rate;
        // If GST rate is 0 or NULL, use 18%
        if ($gstRate <= 0) {
            $gstRate = 18;}
        // Amount after discount
        $amountAfterDiscount = max(
            0,
            (float) ($order->subtotal ?? 0)
            - (float) ($order->discount ?? 0)
        );
        // GST Inclusive Calculation
        // Example:
        // 1769 × 18 / 118 = 269.85
        $gstAmount = 0;
    
        if ($gstRate > 0) {
            $gstAmount = ($amountAfterDiscount * $gstRate) / (100 + $gstRate);
        }
        $gstBreakdown = [
            'gst_rate' => $gstRate,
    
            'gst_rate_label' => 'GST (' .
                rtrim(
                    rtrim(number_format($gstRate, 2), '0'),
                    '.'
                ) .
                '% inclusive)',
    
            'gst_total' => $gstAmount,
        ];
    
        return view(
            'admin.order.show',compact('order','gstBreakdown'
            )
        );
    }
    /*
    |--------------------------------------------------
    | ORDER STATUS UPDATE
    |--------------------------------------------------
    */
    public function statusUpdate(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required',
        ]);

        $payload = [
            'order_status' => $request->order_status,
        ];

        if ($request->order_status === 'delivered') {
            if (! $order->delivered_at) {
                $payload['delivered_at'] = now();
            }

            if (! $order->returnable_until) {
                $payload['returnable_until'] = now()->addDays(7)->toDateString();
            }
        }

        if ($request->order_status === 'cancelled' && $order->cancel_requested_at && ! $order->cancel_approved_at) {
            $payload['cancelled_at'] = now();
            $payload['cancel_approved_at'] = now();
            $payload['cancel_rejected_at'] = null;
        }

        if ($request->order_status === 'returns' && $order->return_requested_at && ! $order->return_approved_at) {
            $payload['return_approved_at'] = now();
            $payload['return_rejected_at'] = null;
            $payload['returned_at'] = now();
        }

        $wasCancelled = $order->order_status === 'cancelled';

        $order->update($payload);

        if (! $wasCancelled && $request->order_status === 'cancelled') {
            $this->sendOrderCancelledEmails($order->fresh());
        }

        return redirect()
            ->route('order.index')
            ->with('success', 'Order Status Updated Successfully');
    }

    public function approveCancelRequest(Request $request, Order $order)
    {
        $request->validate([
            'decision_note' => 'nullable|string|max:1000',
        ]);

        if (! $order->cancel_requested_at || $order->cancel_approved_at) {
            return back()->with('error', 'No pending cancellation request found.');
        }

        $order->update([
            'order_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_approved_at' => now(),
            'cancel_rejected_at' => null,
            'cancel_decision_note' => $request->decision_note,
        ]);

        if ($order->payment_method === 'razorpay' && (float) $order->paid_amount > 0) {
            $alreadyRequested = $order->refunds()
                ->where('status', '!=', 'rejected')
                ->sum('amount');

            $availableRefund = max(0, (float) $order->paid_amount - (float) $alreadyRequested);

            if ($availableRefund > 0) {
                Refund::create([
                    'order_id' => $order->id,
                    'amount' => $availableRefund,
                    'reason' => 'Auto-created after approved cancellation request.',
                    'status' => 'pending',
                ]);
            }
        }

        $this->sendOrderCancelledEmails($order->fresh());

        return back()->with('success', 'Cancellation request approved successfully.');
    }

    public function rejectCancelRequest(Request $request, Order $order)
    {
        $request->validate([
            'decision_note' => 'nullable|string|max:1000',
        ]);

        if (! $order->cancel_requested_at || $order->cancel_approved_at) {
            return back()->with('error', 'No pending cancellation request found.');
        }

        $order->update([
            'cancel_rejected_at' => now(),
            'cancel_approved_at' => null,
            'cancel_decision_note' => $request->decision_note,
        ]);

        return back()->with('success', 'Cancellation request rejected successfully.');
    }

    private function sendOrderCancelledEmails(?Order $order): void
    {
        if (! $order) {
            return;
        }

        $order->loadMissing(['user', 'items.product', 'shippingAddress']);

        $customerEmail = $order->user?->email;
        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new OrderCancelled($order));
            } catch (\Throwable $e) {
                Log::error('Order cancelled user email failed: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'email' => $customerEmail,
                ]);
            }
        } else {
            Log::warning('Order cancelled user email skipped: missing customer email.', [
                'order_id' => $order->id,
            ]);
        }

        $adminEmail = config('mail.admin_order_email') ?: config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new OrderCancelledAdmin($order));
            } catch (\Throwable $e) {
                Log::error('Order cancelled admin email failed: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'email' => $adminEmail,
                ]);
            }
        }
    }

    public function approveReturnRequest(Request $request, Order $order)
    {
        $request->validate([
            'pickup_date' => 'nullable|date',
            'decision_note' => 'nullable|string|max:1000',
        ]);

        if (! $order->return_requested_at || $order->return_approved_at) {
            return back()->with('error', 'No pending return or exchange request found.');
        }

        $order->update([
            'order_status' => 'returns',
            'return_approved_at' => now(),
            'return_rejected_at' => null,
            'return_decision_note' => $request->decision_note,
            'pickup_date' => $request->pickup_date,
            'returned_at' => now(),
        ]);

        if (
            $order->return_request_type === 'return' &&
            $order->payment_method === 'razorpay' &&
            (float) $order->paid_amount > 0
        ) {
            $alreadyRequested = $order->refunds()
                ->where('status', '!=', 'rejected')
                ->sum('amount');

            $availableRefund = max(0, (float) $order->paid_amount - (float) $alreadyRequested);

            if ($availableRefund > 0) {
                Refund::create([
                    'order_id' => $order->id,
                    'amount' => $availableRefund,
                    'reason' => 'Auto-created after approved return request.',
                    'status' => 'pending',
                ]);
            }
        }

        return back()->with('success', 'Return or exchange request approved successfully.');
    }

    public function rejectReturnRequest(Request $request, Order $order)
    {
        $request->validate([
            'decision_note' => 'nullable|string|max:1000',
        ]);

        if (! $order->return_requested_at || $order->return_approved_at) {
            return back()->with('error', 'No pending return or exchange request found.');
        }

        $order->update([
            'return_rejected_at' => now(),
            'return_approved_at' => null,
            'return_decision_note' => $request->decision_note,
            'pickup_date' => null,
        ]);

        return back()->with('success', 'Return or exchange request rejected successfully.');
    }

    public function receiveRemainingPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);
    
        $amount = (float) $request->amount;
    
        if ($amount > $order->due_amount) {
            return back()->with('error', 'Amount cannot exceed due amount.');
        }
    
        DB::beginTransaction();
    
        try {
            // Create a new payment record directly
            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'payment_method' => 'manual',
                'payment_stage' => 'remaining',
                'status' => 'success',
                'paid_at' => now(),
                'note' => 'Paid from admin panel',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Update order payment totals
            $newPaid = $order->paid_amount + $amount;
            $newDue = max(0, $order->grand_total - $newPaid);
    
            $order->update([
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
            ]);
    
            DB::commit();
    
            // Redirect to index with success message
            return redirect()
                ->route('order.index')
                ->with('success', 'Payment of ₹' . number_format($amount, 2) . ' has been marked as paid successfully.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------
    | PAYMENT STATUS UPDATE
    |--------------------------------------------------
    */
    public function paymentStatusUpdate(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Payment Status Updated Successfully');
    }

    /*
    |--------------------------------------------------
    | INVOICE
    |--------------------------------------------------
    */
    public function invoice(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'orderPayments', 'refunds', 'addresses']);

        return view('admin.order.invoice', compact('order'));
    }

    /*
    |--------------------------------------------------
    | PRINT INVOICE
    |--------------------------------------------------
    */
    public function printInvoice(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'orderPayments', 'refunds']);
        return view('admin.order.print', compact('order'));
    }

    /*
    |--------------------------------------------------
    | PDF DOWNLOAD
    |--------------------------------------------------
    */
    public function downloadPdf(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'orderPayments', 'refunds', 'addresses']);

        $pdf = Pdf::loadView('admin.order.invoice', compact('order'))
            ->setPaper('a4');

        $filename = 'invoice-' . ($order->order_id ?? $order->id) . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /*
    |--------------------------------------------------
    | EXPORT CSV
    |--------------------------------------------------
    */
    public function export(Request $request)
    {
        $orders = $this->buildOrderQuery($request)->latest()->get();
        $fileName = 'orders_' . date('Y-m-d_His') . '.csv';

        return $this->streamCsvDownload($fileName, $this->orderExportHeaders(), function ($file) use ($orders) {
            foreach ($orders as $order) {
                $baseRow = [
                    $order->order_id,
                    $order->invoice_no ?? '',
                    $order->user->name ?? 'Guest',
                    $order->user->email ?? '',
                    $order->user->phone ?? '',
                    $order->payment_method ?? '',
                    $order->payment_status ?? '',
                    $order->order_status ?? '',
                    $order->subtotal ?? 0,
                    $order->discount ?? 0,
                    $order->shipping_charge ?? 0,
                    $order->grand_total ?? 0,
                    $order->paid_amount ?? 0,
                    $order->due_amount ?? 0,
                    // Split order date and time into separate columns for Excel compatibility
                    $order->created_at ? $order->created_at->format('d-m-Y') : '',
                    $order->created_at ? $order->created_at->format('H:i:s') : '', // Time column
                ];

                if ($order->items->isEmpty()) {
                    fputcsv($file, array_merge($baseRow, ['', '', '', '', '']));
                    continue;
                }

                foreach ($order->items as $item) {
                    fputcsv($file, array_merge($baseRow, [
                        $item->product->sku ?? '',
                        $item->product->name ?? '',
                        $item->quantity,
                        $item->price,
                        $item->total,
                    ]));
                }
            }
        });
    }

    public function importSample()
    {
        $fileName = 'orders_import_sample.csv';

        return $this->streamCsvDownload($fileName, $this->orderExportHeaders(), function ($file) {
            fputcsv($file, [
                'ORD-20260620-1001',
                'INV-2026060001',
                'John Doe',
                'john@example.com',
                '9876543210',
                'cod',
                'paid',
                'processing',
                '1000',
                '0',
                '50',
                '1050',
                '1050',
                '0',
                date('d-m-Y'), // Date column
                date('H:i:s'), // Time column
                'SKU-001',
                'Sample Product',
                '2',
                '500',
                '1000',
            ]);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $parsed = $this->parseCsvFile($request->file('file'));

        if (empty($parsed['rows'])) {
            return back()->with('error', 'CSV file is empty or invalid.');
        }

        $grouped = [];
        foreach ($parsed['rows'] as $index => $row) {
            $orderKey = $this->csvValue($row, 'order_id') ?: 'ROW-' . ($index + 2);
            $grouped[$orderKey][] = $row;
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($grouped as $orderKey => $rows) {
            $firstRow = $rows[0];

            try {
                DB::transaction(function () use ($firstRow, $rows, &$imported, &$skipped, $orderKey) {
                    $email = $this->csvValue($firstRow, 'customer_email');
                    $name = $this->csvValue($firstRow, 'customer_name', 'Imported Customer');

                    if (!$email) {
                        throw new \RuntimeException("Order {$orderKey}: customer email is required.");
                    }

                    $user = User::where('email', $email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'phone' => $this->csvValue($firstRow, 'customer_phone'),
                            'password' => Hash::make(Str::random(16)),
                            'status' => 1,
                        ]);
                    }

                    $orderId = $this->csvValue($firstRow, 'order_id');
                    if (!$orderId) {
                        $orderId = Order::generateOrderId();
                    }

                    $existing = Order::where('order_id', $orderId)->first();
                    if ($existing) {
                        $skipped++;
                        return;
                    }

                    $subtotal = (float) $this->csvValue($firstRow, 'subtotal', 0);
                    $discount = (float) $this->csvValue($firstRow, 'discount', 0);
                    $shipping = (float) $this->csvValue($firstRow, 'shipping_charge', 0);
                    $grandTotal = (float) $this->csvValue($firstRow, 'grand_total', 0);
                    $paidAmount = (float) $this->csvValue($firstRow, 'paid_amount', $grandTotal);
                    $dueAmount = (float) $this->csvValue($firstRow, 'due_amount', max(0, $grandTotal - $paidAmount));

                    // Handle date/time parse for import
                    $date = $this->csvValue($firstRow, 'order_date');
                    $time = $this->csvValue($firstRow, 'order_time');
                    $createdAt = null;
                    if ($date && $time) {
                        // Both present, try to combine
                        $createdAt = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $date . ' ' . $time);
                    } elseif ($date) {
                        // Only date, use date with midnight
                        $createdAt = \Carbon\Carbon::createFromFormat('d-m-Y', $date)->startOfDay();
                    }

                    $order = Order::create([
                        'user_id' => $user->id,
                        'order_id' => $orderId,
                        'invoice_no' => $this->csvValue($firstRow, 'invoice_no') ?: null,
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'shipping_charge' => $shipping,
                        'grand_total' => $grandTotal > 0 ? $grandTotal : $subtotal - $discount + $shipping,
                        'paid_amount' => $paidAmount,
                        'due_amount' => $dueAmount,
                        'payment_method' => $this->csvValue($firstRow, 'payment_method', 'cod'),
                        'payment_status' => $this->csvValue($firstRow, 'payment_status', 'paid'),
                        'order_status' => $this->csvValue($firstRow, 'order_status', 'processing'),
                        'payment_type' => 'full',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt ?: now(),
                    ]);

                    foreach ($rows as $row) {
                        $sku = $this->csvValue($row, 'product_sku');
                        if (!$sku) {
                            continue;
                        }

                        $product = Product::where('sku', $sku)->first();
                        if (!$product) {
                            throw new \RuntimeException("Order {$orderId}: product SKU {$sku} not found.");
                        }

                        $quantity = (int) $this->csvValue($row, 'quantity', 1);
                        $price = (float) $this->csvValue($row, 'item_price', $product->selling_price);
                        $total = (float) $this->csvValue($row, 'item_total', $price * $quantity);

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'total' => $total,
                        ]);
                    }

                    $imported++;
                });
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        $message = "Import complete: {$imported} order(s) imported";
        if ($skipped > 0) {
            $message .= ", {$skipped} skipped (already exist)";
        }

        return redirect()
            ->route('order.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function transactions(Request $request)
    {
        $query = OrderPayment::with(['order.user']);

        // Search by Order ID
        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->latest()->paginate(20);

        // keep filters in pagination
        $transactions->appends($request->all());

        return view('admin.transaction.index', compact('transactions'));
    }
}