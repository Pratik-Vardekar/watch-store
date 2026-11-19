<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderPayment;
use App\Models\OrderAddress;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'invoice_no',

        'subtotal',
        'discount',
        'shipping_charge',
        'grand_total',

        'paid_amount',
        'due_amount',

        'payment_method',
        'payment_type',
        'payment_status',
        'order_status',

        'transaction_id',
        'payment_gateway',
        'payment_id',
        'razorpay_order_id',

        'invoice_date',
        'due_date',

        'tracking_id',
        'courier_name',
        'tracking_url',

        'admin_note',
        'notes',

        'delivered_at',
        'cancelled_at',
        'shipped_at',

        // Cancel request workflow
        'cancel_requested_at',
        'cancel_request_reason',
        'cancel_request_note',
        'cancel_approved_at',
        'cancel_rejected_at',
        'cancel_decision_note',

        // Return/exchange workflow
        'return_requested_at',
        'return_request_type',
        'return_reason',
        'return_request_note',
        'return_approved_at',
        'return_rejected_at',
        'return_decision_note',
        'returnable_until',
        'pickup_date',
        'returned_at',

        'full_payment_discount',
        'coupon_code',
        'coupon_discount',

        // Shiprocket
        'shiprocket_order_id',
        'shiprocket_shipment_id',
        'shiprocket_courier_id',
        'shiprocket_status',
        'shiprocket_label_url',
        'shiprocket_pickup_scheduled_at',
        'shiprocket_error',
        'shiprocket_synced_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',

        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'shipped_at' => 'datetime',

        'cancel_requested_at' => 'datetime',
        'cancel_approved_at' => 'datetime',
        'cancel_rejected_at' => 'datetime',

        'return_requested_at' => 'datetime',
        'return_approved_at' => 'datetime',
        'return_rejected_at' => 'datetime',
        'returned_at' => 'datetime',

        'returnable_until' => 'date',
        'pickup_date' => 'date',

        'shiprocket_pickup_scheduled_at' => 'datetime',
        'shiprocket_synced_at' => 'datetime',
    ];

    protected $appends = [
        'discount_amount',
        'total_paid',
        'payment_status_auto',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->invoice_no)) {
                $order->invoice_no = static::generateNextInvoiceNumber();
            }
            if (empty($order->invoice_date)) {
                $order->invoice_date = now()->toDateString();
            }
        });
    }

    /**
     * Format number as INV001, INV002, etc.
     */
    public static function formatInvoiceNumber(int $number): string
    {
        return sprintf('INV%03d', max(1, $number));
    }

    /**
     * Generate sequential invoice number in format INV001, INV002, etc.
     */
    public static function generateNextInvoiceNumber(?int $orderId = null): string
    {
        try {
            $latestNumber = static::whereNotNull('invoice_no')
                ->where('invoice_no', 'like', 'INV%')
                ->pluck('invoice_no')
                ->filter(function ($inv) {
                    return preg_match('/^INV(\d{3,8})$/', (string) $inv, $matches)
                        && (int) $matches[1] >= 1
                        && (int) $matches[1] < 100000000;
                })
                ->map(function ($inv) {
                    preg_match('/^INV(\d+)$/', (string) $inv, $matches);
                    return (int) ($matches[1] ?? 0);
                })
                ->max();

            if ($latestNumber && $latestNumber >= 1) {
                $next = $latestNumber + 1;
                while (static::where('invoice_no', static::formatInvoiceNumber($next))->exists()) {
                    $next++;
                }
                return static::formatInvoiceNumber($next);
            }
        } catch (\Throwable $e) {
            // DB or table exception fallback
        }

        if ($orderId && $orderId > 0) {
            $candidate = $orderId;
            try {
                while (static::where('invoice_no', static::formatInvoiceNumber($candidate))->exists()) {
                    $candidate++;
                }
            } catch (\Throwable $e) {}
            return static::formatInvoiceNumber($candidate);
        }

        $start = 1;
        try {
            while (static::where('invoice_no', static::formatInvoiceNumber($start))->exists()) {
                $start++;
            }
        } catch (\Throwable $e) {}

        return static::formatInvoiceNumber($start);
    }

    /**
     * Format invoice number to always conform to INV001 format
     */
    public function getInvoiceNoAttribute($value): ?string
    {
        if (!empty($value)) {
            if (preg_match('/^INV\d{10,}$/', (string) $value) || str_starts_with((string) $value, 'INV-ORD')) {
                return static::formatInvoiceNumber($this->id ?: 1);
            }
            if (preg_match('/^INV(\d{1,2})$/', (string) $value, $matches)) {
                return static::formatInvoiceNumber((int) $matches[1]);
            }
            return $value;
        }

        if (!empty($this->id)) {
            return static::formatInvoiceNumber($this->id);
        }

        return 'INV001';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getValidItemsAttribute()
    {
        $items = $this->items->unique(function ($item) {
            return $item->product_id . '_' . ($item->variant_id ?? '0');
        });

        if ($items->count() <= 1) {
            return $items;
        }

        $orderSubtotal = (float) ($this->subtotal > 0 ? $this->subtotal : $this->grand_total);
        $itemsSum = (float) $items->sum(function ($it) {
            $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
            $q = max(1, (int) $it->quantity);
            return (float) ($it->total > 0 ? $it->total : ($p * $q));
        });

        if ($orderSubtotal > 0 && ($itemsSum - $orderSubtotal) > 1.0) {
            // Check if any single item matches order subtotal
            $matchingItem = $items->first(function ($it) use ($orderSubtotal) {
                $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
                $q = max(1, (int) $it->quantity);
                $lineTotal = (float) ($it->total > 0 ? $it->total : ($p * $q));
                return abs($lineTotal - $orderSubtotal) < 1.0;
            });

            if ($matchingItem) {
                return collect([$matchingItem]);
            }

            // Otherwise filter out items whose individual total exceeds the order total
            $filtered = $items->filter(function ($it) use ($orderSubtotal) {
                $p = (float) ($it->price > 0 ? $it->price : ($it->product->selling_price ?? $it->product->price ?? 0));
                $q = max(1, (int) $it->quantity);
                $lineTotal = (float) ($it->total > 0 ? $it->total : ($p * $q));
                return $lineTotal <= ($orderSubtotal + 1.0);
            });

            if ($filtered->isNotEmpty()) {
                return $filtered;
            }
        }

        return $items;
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'billing');
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('type', 'shipping');
    }
    public function firstProduct()
    {
        $item = $this->relationLoaded('items')
            ? $this->items->first()
            : $this->items()->with('product')->first();

        return $item?->product;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDiscountAmountAttribute()
    {
        return (float) (
            ($this->discount ?? 0)
            + ($this->full_payment_discount ?? 0)
        );
    }

    public function getTotalPaidAttribute()
    {
        return $this->orderPayments()->sum('amount');
    }

    public function getPaymentStatusAutoAttribute()
    {
        if ($this->total_paid <= 0) {
            return 'pending';
        }

        if ($this->total_paid < $this->grand_total) {
            return 'partial';
        }

        return 'paid';
    }

    public function getPaymentBreakdown(): array
    {
        $grandTotal = (float) $this->grand_total;
        $status = strtolower((string) ($this->payment_status ?? ''));
        $type = strtolower((string) ($this->payment_type ?? ''));
        $isPaid = ($status === 'paid');
        $storedPaid = (float) ($this->paid_amount ?? 0);
        $storedDue = isset($this->due_amount) && $this->due_amount !== null ? (float) $this->due_amount : null;

        if ($isPaid) {
            return [
                'paid' => $grandTotal,
                'due' => 0.0,
                'is_paid' => true,
                'is_partial' => false,
            ];
        }

        if ($status === 'pending' && $type !== 'partial' && $storedPaid <= 0) {
            return [
                'paid' => 0.0,
                'due' => $grandTotal,
                'is_paid' => false,
                'is_partial' => false,
            ];
        }

        // When due_amount is recorded on the order (e.g. partial COD order)
        if ($storedDue !== null && $storedDue > 0) {
            $due = min($grandTotal, $storedDue);
            $paid = max(0.0, round($grandTotal - $due, 2));
        } elseif ($storedPaid > 0) {
            $paid = min($grandTotal, $storedPaid);
            $due = max(0.0, round($grandTotal - $paid, 2));
        } else {
            $paymentsTotal = (float) ($this->orderPayments ? $this->orderPayments->where('status', 'success')->sum('amount') : 0);
            $paid = min($grandTotal, max(0.0, $paymentsTotal));
            $due = max(0.0, round($grandTotal - $paid, 2));
        }

        $isPartial = ($paid > 0 && $due > 0);

        return [
            'paid' => $paid,
            'due' => $due,
            'is_paid' => ($due <= 0 && $paid >= $grandTotal),
            'is_partial' => $isPartial,
        ];
    }

    public function invoicePdfFilename(): string
    {
        return 'Invoice-' . ($this->invoice_no ?? $this->order_id ?? $this->id) . '.pdf';
    }
    public function returnDeadline()
    {
        if ($this->returnable_until) {
            return $this->returnable_until;
        }

        if ($this->delivered_at) {
            return $this->delivered_at->copy()->addDays(7);
        }

        if (strtolower($this->order_status ?? '') === 'delivered' && $this->created_at) {
            return $this->created_at->copy()->addDays(7);
        }

        return null;
    }

    public function expectedDeliveryDate()
    {
        if ($this->delivered_at) {
            return $this->delivered_at;
        }

        if ($this->shipped_at) {
            return $this->shipped_at->copy()->addDays(3);
        }

        if ($this->created_at) {
            return $this->created_at->copy()->addDays(7);
        }

        return null;
    }

    public function hasPendingCancelRequest(): bool
    {
        return (bool) ($this->cancel_requested_at && ! $this->cancel_approved_at && ! $this->cancel_rejected_at);
    }

    public function hasPendingReturnRequest(): bool
    {
        return (bool) ($this->return_requested_at && ! $this->return_approved_at && ! $this->return_rejected_at);
    }

    public function canRequestReturn(): bool
    {
        if (strtolower($this->order_status ?? '') !== 'delivered') {
            return false;
        }

        if ($this->hasPendingReturnRequest() || $this->return_approved_at) {
            return false;
        }

        $deadline = $this->returnDeadline();

        return $deadline && now()->lte($deadline);
    }

    public function canRequestCancel(): bool
    {
        return in_array(strtolower($this->order_status ?? ''), ['pending', 'confirmed', 'processing'], true)
            && ! $this->hasPendingCancelRequest()
            && ! $this->cancel_approved_at;
    }

    public function gstBreakdown(): array
    {
        $gstRate = (float) ($this->gst_rate ?? 18);
        if ($gstRate <= 0) {
            $gstRate = 18.0;
        }
        $subtotal = (float) ($this->subtotal ?? 0);
        $discount = (float) ($this->discount ?? 0);
        $netAmount = max(0, $subtotal - $discount);

        // GST Inclusive Calculation
        $gstAmount = ($netAmount * $gstRate) / (100 + $gstRate);
        $baseAmount = $netAmount - $gstAmount;

        // Get GST details per product if available
        $gstDetails = [];
        foreach ($this->items as $item) {
            if ($item->product) {
                $productGstRate = $item->product->getGstRate();
                $itemTotal = (float) $item->total;
                $itemGst = ($itemTotal * $productGstRate) / (100 + $productGstRate);

                $rateKey = (string) $productGstRate . '%';
                if (!isset($gstDetails[$rateKey])) {
                    $gstDetails[$rateKey] = [
                        'rate' => $productGstRate,
                        'total' => 0,
                    ];
                }
                $gstDetails[$rateKey]['total'] += $itemGst;
            }
        }

        // Format for display
        $formattedDetails = [];
        foreach ($gstDetails as $key => $detail) {
            $formattedDetails[$key] = [
                'total' => round($detail['total'], 2),
            ];
        }

        return [
            'gst_rate' => $gstRate,
            'gst_rate_label' => 'GST (' . rtrim(rtrim(number_format($gstRate, 2), '0'), '.') . '% inclusive)',
            'gst_total' => round($gstAmount, 2),
            'base_amount' => round($baseAmount, 2),
            'net_amount' => $netAmount,
            'gst_details' => $formattedDetails,
        ];
    }

    /**
     * Check if a specific user has already used a given coupon code in any non-cancelled order.
     *
     * @param int|string|null $userId
     * @param string|null $couponCode
     * @return bool
     */
    public static function isCouponUsedByUser($userId, ?string $couponCode): bool
    {
        if (!$userId || empty($couponCode)) {
            return false;
        }

        $code = strtoupper(trim($couponCode));

        return static::where('user_id', $userId)
            ->whereNotIn('order_status', ['cancelled'])
            ->where(function ($query) use ($code) {
                $query->where('notes', 'LIKE', 'Coupon: ' . $code . '%')
                    ->orWhere('notes', 'LIKE', '%Coupon: ' . $code . '%');

                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'coupon_code')) {
                        $query->orWhereRaw('UPPER(coupon_code) = ?', [$code]);
                    }
                } catch (\Throwable $e) {
                    // Ignore schema check error if table/column isn't available
                }
            })
            ->exists();
    }
}
