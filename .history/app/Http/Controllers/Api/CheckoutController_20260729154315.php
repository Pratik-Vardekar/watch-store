<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Models\Wishlist;
use App\Models\ShippingSetting;
use App\Services\SmsAlertService;
class CheckoutController extends Controller
{
    /*
    |-----------------------------------------
    | PLACE ORDER (ONLY CREATE PENDING ORDER)
    |-----------------------------------------
    */
    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'source' => 'required|in:cart,buy_now',
                'payment_type' => 'required|in:full,partial',
                'address_id' => 'required|exists:addresses,id',
                'coupon_code' => 'nullable'
            ]);

            $user = auth('sanctum')->user();

            $items = collect();

            if ($request->source === 'cart') {

                $items = Cart::with('product.gstSetting')
                    ->where('user_id', $user->id)
                    ->get();

                if ($items->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Cart is empty'
                    ], 422);
                }
            }

            if ($request->source === 'buy_now') {

                $buyNow = DB::table('buy_nows')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();

                if (!$buyNow) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Buy now item not found'
                    ], 422);
                }

                $product = Product::with('gstSetting')->findOrFail($buyNow->product_id);

                $items->push((object)[
                    'product_id' => $buyNow->product_id,
                    'variant_id' => $buyNow->variant_id,
                    'quantity' => $buyNow->quantity,
                    'product' => $product,
                    'coupon_code' => $buyNow->coupon_code ?? null
                ]);
            }

            $couponCode = $request->coupon_code ?? $items->pluck('coupon_code')->filter()->first();

            $subtotal = 0;
            $gstTotal = 0;
            foreach ($items as $item) {
                $sellingPrice = (float) ($item->product->selling_price ?? $item->product->price ?? 0);
                $subtotal += $sellingPrice * $item->quantity;

                if ($item->product) {
                    $taxInfo = $item->product->getInclusiveTaxInfo($sellingPrice);
                    $gstTotal += ((float) ($taxInfo['tax_amount'] ?? 0)) * $item->quantity;
                }
            }
            $gstTotal = round($gstTotal, 2);

            $couponDiscountAmount = 0;

            if ($couponCode) {
                $coupon = DB::table('coupons')
                    ->where('code', $couponCode)
                    ->where('status', 1)
                    ->first();

                if ($coupon) {

                    $today = date('Y-m-d');

                    if (
                        $coupon->start_date <= $today &&
                        $coupon->end_date >= $today &&
                        $subtotal >= $coupon->minimum_amount
                    ) {

                        if ($coupon->type === 'fixed') {
                            $couponDiscountAmount = (float) $coupon->value;
                        } else {
                            $couponDiscountAmount = ($subtotal * $coupon->value) / 100;

                            if ($coupon->maximum_discount) {
                                $couponDiscountAmount = min($couponDiscountAmount, $coupon->maximum_discount);
                            }
                        }

                        $couponDiscountAmount = min($couponDiscountAmount, $subtotal);
                    }
                }
            }

            $discount = round($couponDiscountAmount, 2);

            $shippingSetting = ShippingSetting::where('status', 1)->first();

            $shipping = 0;
            
            if ($shippingSetting) {
            
                $freeShippingAmount = $shippingSetting->free_shipping_amount;
            
                $shipping = $subtotal >= $freeShippingAmount
                    ? 0
                    : $shippingSetting->shipping_charge;
            }

            // Prices are GST-inclusive, so GST is informational and not added again.
            $grandTotal = round($subtotal - $discount + $shipping, 2);
            
            // 5% discount for full payment
            $fullPaymentDiscount = 0;
            
            if ($request->payment_type === 'full') {
                $fullPaymentDiscount = round($grandTotal * 0.05, 2);
                $grandTotal = round($grandTotal - $fullPaymentDiscount, 2);
            }
            
            $payNow = $request->payment_type === 'partial'
                ? round($grandTotal * 0.30, 2)
                : $grandTotal;
            
            $dueAmount = $grandTotal - $payNow;

            $orderNumber = 'AGW-' . now()->format('ymd') . rand(1000,9999);

            $order = Order::create([
                'user_id' => $user->id,
                'order_id' => $orderNumber,

                'subtotal' => $subtotal,
                'discount' => $discount + $fullPaymentDiscount,
                'shipping_charge' => $shipping,

                'cgst_amount' => $gstTotal / 2,
                'sgst_amount' => $gstTotal / 2,
                'igst_amount' => 0,

                'taxable_amount' => $subtotal,

                'grand_total' => $grandTotal,
                'paid_amount' => 0,
                'due_amount' => $dueAmount,

                'payment_method' => 'razorpay',
                'payment_type' => $request->payment_type,

                'payment_status' => 'pending',
                'order_status' => 'pending',

                'order_source' => $request->source,
            'coupon_code' => $couponCode,
'coupon_discount' => $discount,
'full_payment_discount' => $fullPaymentDiscount,
            ]);

            $address = Address::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            DB::table('order_addresses')->insert([
                'order_id' => $order->id,
                'type' => 'shipping',
                'name' => $address->name,
                'phone' => $address->phone,
                'alternate_phone' => $address->alternate_phone,
                'address' => $address->flat . ', ' . $address->area,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'pincode' => $address->pincode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id ?? null,
                    'quantity' => $item->quantity,
                    'price' => $item->product->selling_price,
                    'total' => $item->product->selling_price * $item->quantity
                ]);
            }

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $razorpayOrder = $api->order->create([
                'receipt' => $orderNumber,
                'amount' => (int) round($payNow * 100),
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            $order->update([
                'razorpay_order_id' => $razorpayOrder['id']
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created (pending payment)',

                'order_id' => $order->id,
                'order_number' => $orderNumber,

                'pay_now' => $payNow,
                'due_amount' => $dueAmount,
                'gst_total' => $gstTotal,

                'razorpay' => [
                    'key' => env('RAZORPAY_KEY'),
                    'order_id' => $razorpayOrder['id'],
                    'amount' => (int) round($payNow * 100),
                    'currency' => 'INR',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    |-----------------------------------------
    | VERIFY PAYMENT (FINAL ORDER CONFIRMATION)
    |-----------------------------------------
    */
    public function verifyPayment(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'razorpay_payment_id' => 'required',
                'razorpay_order_id' => 'required',
                'razorpay_signature' => 'required',
            ]);

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            // Verify signature
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            $order = Order::findOrFail($request->order_id);

            // Check if payment is already processed
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment already completed'
                ], 422);
            }

            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $paidAmount = $payment['amount'] / 100;

            // Calculate totals
            $advanceAmount = $paidAmount;
            $remainingAmount = $order->grand_total - $advanceAmount;

            // Determine payment stage
            $paymentStage = ($order->payment_type === 'partial') ? 'advance' : 'full';

            // Create payment record for the advance/full payment
            DB::table('order_payments')->insert([
                'order_id' => $order->id,
                'amount' => $paidAmount,
                'type' => 'payment',
                'payment_method' => 'razorpay',
                'reference' => $request->razorpay_payment_id,
                'transaction_id' => $request->razorpay_payment_id,
                'status' => 'success',
                'paid_at' => now(),
                'payment_stage' => $paymentStage,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ If partial payment, create a second entry for remaining amount
            if ($order->payment_type === 'partial' && $remainingAmount > 0) {
                DB::table('order_payments')->insert([
                    'order_id' => $order->id,
                    'amount' => $remainingAmount,
                    'type' => 'payment',
                    'payment_method' => 'razorpay',
                    'reference' => null,
                    'transaction_id' => null,
                    'status' => 'pending',
                    'paid_at' => null,
                    'payment_stage' => 'remaining',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Generate remaining payment link
                $remainingOrder = $api->order->create([
                    'receipt' => $order->order_id . '-remaining',
                    'amount' => (int) round($remainingAmount * 100),
                    'currency' => 'INR',
                    'payment_capture' => 1
                ]);

                $order->update([
                    'remaining_payment_link' => $remainingOrder['id'],
                    'razorpay_order_id' => $remainingOrder['id']
                ]);

                // Update the pending remaining payment record with the new order ID
                DB::table('order_payments')
                    ->where('order_id', $order->id)
                    ->where('payment_stage', 'remaining')
                    ->where('status', 'pending')
                    ->update([
                        'reference' => $remainingOrder['id'],
                        'transaction_id' => $remainingOrder['id'],
                        'updated_at' => now()
                    ]);
            }

            // Update order
            $newPaidAmount = $order->paid_amount + $paidAmount;
            $newDueAmount = max(0, round($order->grand_total - $newPaidAmount, 2));
            
            // ✅ ALWAYS set order_status to 'processing' regardless of payment type
            $paymentStatus = ($newDueAmount > 0) ? 'partial' : 'paid';
            $orderStatus = 'processing';

            $order->update([
                'transaction_id' => $request->razorpay_payment_id,
                'payment_gateway' => 'razorpay',
                'payment_id' => $request->razorpay_order_id,
                'paid_amount' => $newPaidAmount,
                'due_amount' => $newDueAmount,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
            ]);
            // Send order confirmation SMS
try {

    $shippingAddress = DB::table('order_addresses')
        ->where('order_id', $order->id)
        ->first();

    if ($shippingAddress && $shippingAddress->phone) {

        SmsAlertService::send(
            $shippingAddress->phone,
            'Your order with Agriwow is now confirmed. We shall process your order and update the dispatch details as soon as possible. Thank You!'
        );
    }

} catch (\Exception $e) {
    \Log::error('Order SMS Failed: ' . $e->getMessage());
}
// Deduct stock after first successful payment
$this->deductStock($order);

            // ✅ Clear cart and wishlist when payment is successful
            $this->clearUserPurchasedItems($order);

            DB::commit();

            // Prepare response
            $response = [
                'status' => true,
                'message' => $newDueAmount > 0 ? 'Advance payment successful' : 'Payment verified successfully',
                'data' => [
                    'order_id' => $order->order_id,
                    'paid_amount' => $paidAmount,
                    'total_paid' => $newPaidAmount,
                    'due_amount' => $newDueAmount,
                    'payment_status' => $paymentStatus,
                    'order_status' => $orderStatus,
                    'payment_type' => $order->payment_type,
                    'is_completed' => $newDueAmount == 0,
                ]
            ];

            // Include remaining payment info if needed
            if ($newDueAmount > 0) {
                $response['data']['remaining_payment'] = [
                    'due_amount' => $newDueAmount,
                    'razorpay_order_id' => $order->razorpay_order_id,
                    'key' => env('RAZORPAY_KEY'),
                    'amount' => (int) round($newDueAmount * 100),
                    'currency' => 'INR',
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /*
    |-----------------------------------------
    | VERIFY REMAINING PAYMENT
    |-----------------------------------------
    */
    public function verifyRemainingPayment(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'razorpay_payment_id' => 'required',
                'razorpay_order_id' => 'required',
                'razorpay_signature' => 'required',
            ]);

            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);

            $order = Order::findOrFail($request->order_id);
            
            // Check if already fully paid
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'Order is already fully paid'
                ], 422);
            }

            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $paidAmount = $payment['amount'] / 100;

            // ✅ Update the pending remaining payment record
            $updated = DB::table('order_payments')
                ->where('order_id', $order->id)
                ->where('payment_stage', 'remaining')
                ->where('status', 'pending')
                ->update([
                    'amount' => $paidAmount,
                    'reference' => $request->razorpay_payment_id,
                    'transaction_id' => $request->razorpay_payment_id,
                    'status' => 'success',
                    'paid_at' => now(),
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new \Exception('Remaining payment record not found');
            }

            // ✅ FULLY PAID - Keep order_status as processing
            $order->update([
                'transaction_id' => $request->razorpay_payment_id,
                'payment_gateway' => 'razorpay',
                'payment_id' => $request->razorpay_order_id,
                'paid_amount' => $order->grand_total,
                'due_amount' => 0,
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'remaining_payment_link' => null
            ]);

            // ✅ Clear cart and wishlist when fully paid
            $this->clearUserPurchasedItems($order);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Remaining payment successful, order is now fully paid',
                'data' => [
                    'order_id' => $order->order_id,
                    'total_paid' => $order->paid_amount,
                    'due_amount' => 0,
                    'payment_status' => 'paid',
                    'order_status' => 'processing'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
public function remainingPayment(Request $request, Order $order)
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

        // Update pending remaining payment entry
        $payment = DB::table('order_payments')
            ->where('order_id', $order->id)
            ->where('payment_stage', 'remaining')
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            return back()->with('error', 'Remaining payment record not found.');
        }

        DB::table('order_payments')
            ->where('id', $payment->id)
            ->update([
                'amount' => $amount,
                'payment_method' => 'manual',
                'status' => 'success',
                'paid_at' => now(),
                'updated_at' => now(),
                'note' => 'Paid manually from admin'
            ]);

        $newPaid = $order->paid_amount + $amount;
        $newDue = max(0, $order->grand_total - $newPaid);

        $order->update([
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'payment_status' => $newDue == 0 ? 'paid' : 'partial',
        ]);

        DB::commit();

        return back()->with('success', 'Payment updated successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
    /*
    |-----------------------------------------
    | CLEAR USER PURCHASED ITEMS
    |-----------------------------------------
    */
    private function clearUserPurchasedItems($order)
    {
        try {
            $user = auth('sanctum')->user();
            
            if (!$user) {
                return;
            }

            // Get all product IDs from the order
            $productIds = OrderItem::where('order_id', $order->id)
                ->pluck('product_id')
                ->toArray();

            if (empty($productIds)) {
                return;
            }

            // ✅ Clear cart for purchased products (only if order is from cart)
            if ($order->order_source === 'cart') {
                Cart::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->delete();
            }

            // ✅ Clear wishlist for purchased products (always clear wishlist items that are purchased)
            Wishlist::where('user_id', $user->id)
                ->whereIn('product_id', $productIds)
                ->delete();

            // ✅ Also clear buy_nows if any
            DB::table('buy_nows')
                ->where('user_id', $user->id)
                ->whereIn('product_id', $productIds)
                ->delete();

        } catch (\Exception $e) {
            // Log error but don't stop the payment process
            \Log::error('Failed to clear user items: ' . $e->getMessage());
        }
    }
    private function deductStock($order)
{
    try {

        $items = OrderItem::where('order_id', $order->id)->get();

        foreach ($items as $item) {

            // Variant product stock
            if ($item->variant_id) {

                DB::table('product_variants')
                    ->where('id', $item->variant_id)
                    ->decrement('quantity', $item->quantity);

            } else {

                // Simple product stock
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->decrement('quantity', $item->quantity);
            }
        }

    } catch (\Exception $e) {

        \Log::error('Stock deduction failed: ' . $e->getMessage());
    }
}
}