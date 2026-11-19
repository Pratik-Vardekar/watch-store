<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Slider;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Gallery;
use App\Models\BuyNow;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Feature;
use App\Models\Benefit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Contact;
use App\Models\ContactSetting;
use App\Models\SpecialOffer;
use App\Models\Review;
use App\Models\Brand;
use App\Models\ReturnRefund;
use App\Models\WarrantyGuarantee;
use App\Models\ReviewReaction;
use App\Models\ShippingSetting;
use App\Services\CartService;
use App\Services\WishlistService;
use App\Models\Service;
use App\Models\AboutUsSection;
use App\Models\Store;
use App\Models\PlayFunSection;
use App\Models\LearnFunSection;
use App\Models\HowToPlaySection;
use App\Models\HomeFaq;
use App\Models\HomeFaqSection;
use App\Models\HelpCategory;
// REMOVED: use App\Models\HomeTestimonial;
use App\Models\HomeHighlight;
use App\Models\HomeServiceItem;
use App\Models\HomeServiceSectionSetting;
use App\Models\Watches;
use App\Models\ContactEnquiry;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndCondition;
use App\Models\Coupon;
use App\Mail\OrderAdminMail;
use App\Mail\OrderSuccessMail;
use App\Mail\ContactEnquiryUserMail;
use App\Mail\ContactEnquiryAdminMail;
use App\Mail\OrderCancelled;
use App\Mail\OrderCancelledAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

class FrontController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected WishlistService $wishlistService
    ) {
    }

    // ... (all previous code unchanged) ...

    // --- My Account Orders Page ---
    public function myAccountOrders(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $status = $request->input('status');

        $orders = Order::with(['items.product.brand'])
            ->where('user_id', $user->id)
            ->when($status === 'delivered', fn ($q) => $q->where('order_status', 'delivered'))
            ->when($status === 'cancelled', fn ($q) => $q->where('order_status', 'cancelled'))
            ->when($status === 'returns', function ($q) {
                $q->whereNotNull('return_requested_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('frontend.pages.myaccount_orders', compact('user', 'orders'));
    }

    public function myAccountOrderDetails(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'shippingAddress', 'addresses']);

        return view('frontend.pages.order_details', compact('order'));
    }

    // Unified cancelOrder handling both legacy and modern requests (customized to prevent redeclare error)
    public function cancelOrder(Request $request, Order $order)
    {
        // We expect both modern AJAX/form requests (with reason) or legacy direct cancel (no reason).
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        // If request carries 'reason', treat as cancel REQUEST flow, like before
        if ($request->has('reason')) {
            $request->validate([
                'reason' => 'required|string|max:255',
                'note' => 'nullable|string|max:1000',
            ]);

            if (! in_array($order->order_status, ['pending', 'confirmed', 'processing'])) {
                return back()->with('error', 'This order cannot be cancelled now.');
            }

            if ($order->cancel_requested_at && ! $order->cancel_approved_at && ! $order->cancel_rejected_at) {
                return back()->with('error', 'Cancellation request already submitted.');
            }

            $order->update([
                'cancel_requested_at' => now(),
                'cancel_request_reason' => $request->reason,
                'cancel_request_note' => $request->note,
                'cancel_approved_at' => null,
                'cancel_rejected_at' => null,
                'cancel_decision_note' => null,
            ]);

            $message = 'Cancellation request sent to admin.';

            if ($order->payment_method === 'razorpay' && (float) $order->paid_amount > 0) {
                $message .= ' If approved, your refund will be credited to the original payment method within 2-3 working days.';
            }

            return back()->with('success', $message);
        }

        // Legacy cancelOrder logic ("mark as cancelled" directly -- fallback path)
        // If the status already cancelled, just return success
        if ($order->order_status === 'cancelled' || $order->status === 'cancelled') {
            return back()->with('success', 'Order is already cancelled.');
        }

        // Actually cancel the order directly if allowed
        $order->update([
            'order_status' => 'cancelled',
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        try {
            Mail::to($order->user->email)->send(new OrderCancelled($order));
        } catch (\Throwable $e) {
            Log::error('Order cancelled email failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'email' => $order->user->email,
            ]);
        }

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function returnOrder(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'request_type' => 'required|in:return,exchange',
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        if (
            strtolower($order->order_status ?? '') !== 'delivered' ||
            ! $order->returnDeadline() ||
            now()->gt($order->returnDeadline())
        ) {
            return back()->with('error', 'Return or exchange is no longer available for this order.');
        }

        if ($order->return_requested_at && ! $order->return_approved_at && ! $order->return_rejected_at) {
            return back()->with('error', 'Return or exchange request already submitted.');
        }

        $order->update([
            'return_requested_at' => now(),
            'return_request_type' => $request->request_type,
            'return_reason' => $request->reason,
            'return_request_note' => $request->note,
            'return_approved_at' => null,
            'return_rejected_at' => null,
            'return_decision_note' => null,
            'pickup_date' => null,
            'returned_at' => null,
        ]);

        $message = ucfirst($request->request_type) . ' request submitted successfully.';

        if (
            $request->request_type === 'return' &&
            $order->payment_method === 'razorpay' &&
            (float) $order->paid_amount > 0
        ) {
            $message .= ' If approved, your refund will be credited to the original payment method within 2-3 working days.';
        }

        return back()->with('success', $message);
    }

    public function cancelReturn(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        if (
            ! $order->return_requested_at ||
            $order->return_approved_at ||
            $order->return_rejected_at
        ) {
            return back()->with('error', 'No pending return or exchange request found.');
        }

        $order->update([
            'return_requested_at' => null,
            'return_request_type' => null,
            'return_reason' => null,
            'return_request_note' => null,
            'return_approved_at' => null,
            'return_rejected_at' => null,
            'return_decision_note' => null,
            'pickup_date' => null,
        ]);

        return back()->with('success', 'Return or exchange request cancelled.');
    }

    // ... (rest of the controller unchanged) ...

    public function cancelOrderAdmin(Order $order)
    {
        try {
            Mail::to(config('mail.admin_order_email'))->send(new OrderCancelledAdmin($order));
        } catch (\Throwable $e) {
            Log::error('Order cancelled admin email failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'email' => $order->user->email,
            ]);
        }
    }
}
