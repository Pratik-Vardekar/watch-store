<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private function razorpay()
    {
        return new Api(
            env('RAZORPAY_KEY'),
            env('RAZORPAY_SECRET')
        );
    }

    // public function createOrder(Request $request)
    // {
    //     $request->validate([
    //         'order_id' => 'required|integer|exists:orders,id',
    //         'payment_type' => 'required|in:full,partial'
    //     ]);
    
    //     try {
    
    //         $order = Order::find($request->order_id);
    
    //         if (!$order) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Order not found'
    //             ], 404);
    //         }
    
    //         $totalAmount = $order->total_amount;
    
    //         // =========================
    //         // FULL PAYMENT (5% OFF)
    //         // =========================
    //         if ($request->payment_type == 'full') {
    
    //             $discount = env('FULL_PAYMENT_DISCOUNT', 5);
    
    //             $finalAmount = $totalAmount - ($totalAmount * $discount / 100);
    
    //             $order->payment_type = 'full';
    
    //         }
    
    //         // =========================
    //         // PARTIAL PAYMENT (30%)
    //         // =========================
    //         else {
    
    //             $partialPercent = env('PARTIAL_PAYMENT_PERCENT', 30);
    
    //             $finalAmount = ($totalAmount * $partialPercent) / 100;
    
    //             $order->payment_type = 'partial';
    //         }
    
    //         $order->save();
    
    //         // Create Razorpay Order
    //         $api = $this->razorpay();
    
    //         $razorpayOrder = $api->order->create([
    //             'receipt' => 'order_' . $order->id,
    //             'amount' => (int) ($finalAmount * 100),
    //             'currency' => 'INR',
    //             'payment_capture' => 1,
    //             'notes' => [
    //                 'order_id' => $order->id,
    //                 'payment_type' => $request->payment_type
    //             ]
    //         ]);
    
    //         // store razorpay order id
    //         $order->razorpay_order_id = $razorpayOrder['id'];
    //         $order->save();
    
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Payment order created',
    //             'data' => [
    //                 'order_type' => $request->payment_type,
    //                 'amount' => $finalAmount,
    //                 'razorpay_order' => $razorpayOrder
    //             ]
    //         ]);
    
    //     } catch (\Exception $e) {
    
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error creating payment order',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'payment_type' => 'required|in:full,partial'
        ]);

        try {

            $user = auth()->user();

            // =========================
            // STEP 1: GET CART
            // =========================
            $cartItems = Cart::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart is empty'
                ]);
            }

            // =========================
            // STEP 2: CALCULATE TOTAL
            // =========================
            $totalAmount = 0;

            foreach ($cartItems as $item) {
                $totalAmount += $item->price * $item->qty;
            }

            // =========================
            // STEP 3: FIND EXISTING ORDER
            // =========================
            $order = Order::find($request->order_id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Update order details
            $order->total_amount = $totalAmount;
            $order->payment_status = 'pending';
            $order->status = 'pending';
            $order->payment_type = $request->payment_type;
            $order->save();

            // =========================
            // STEP 4: CREATE ORDER ITEMS (Only if order has no items yet)
            // =========================
            if (!OrderItem::where('order_id', $order->id)->exists()) {
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity ?? $item->qty ?? 1,
                        'price' => $item->price,
                        'total' => ($item->quantity ?? $item->qty ?? 1) * $item->price
                    ]);
                }
            }

            // clear cart
            Cart::where('user_id', $user->id)->delete();

            // =========================
            // STEP 5: PAYMENT CALCULATION
            // =========================
            if ($request->payment_type == 'full') {

                $discount = env('FULL_PAYMENT_DISCOUNT', 5);
                $finalAmount = $totalAmount - ($totalAmount * $discount / 100);

            } else {

                $partialPercent = env('PARTIAL_PAYMENT_PERCENT', 30);
                $finalAmount = ($totalAmount * $partialPercent) / 100;
            }

            // =========================
            // STEP 6: CREATE RAZORPAY ORDER
            // =========================
            $api = $this->razorpay();

            $razorpayOrder = $api->order->create([
                'receipt' => 'order_' . $order->id,
                'amount' => (int) ($finalAmount * 100),
                'currency' => 'INR',
                'notes' => [
                    'order_id' => $order->id,
                    'payment_type' => $request->payment_type
                ]
            ]);

            // save razorpay order id
            $order->update([
                'razorpay_order_id' => $razorpayOrder['id']
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $finalAmount,
                    'payment_type' => $request->payment_type,
                    'razorpay_order' => $razorpayOrder
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
    
        try {
    
            $api = $this->razorpay();
    
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);
    
            $order = Order::find($request->order_id);
    
            $order->update([
                'payment_status' => 'paid',
                'payment_id' => $request->razorpay_payment_id,
                'status' => 'processing',
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Payment verified successfully'
            ]);
    
        } catch (\Exception $e) {
    
            Log::error($e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();

            if (
                isset($payload['event']) &&
                $payload['event'] === 'payment.captured'
            ) {
                $payment = $payload['payload']['payment']['entity'];

                Order::where('payment_id', $payment['id'])->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
            }

            return response()->json(['status' => true]);

        } catch (\Exception $e) {

            Log::error('Webhook error: ' . $e->getMessage());

            return response()->json([
                'status' => false
            ], 500);
        }
    }

}