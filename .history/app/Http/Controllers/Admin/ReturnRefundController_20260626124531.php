<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    // =========================
    // STORE REFUND REQUEST
    // =========================
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
        ]);

        // prevent over refund
        $totalRefunded = $order->refunds()->where('status', '!=', 'rejected')->sum('amount');
        $available = $order->paid_amount - $totalRefunded;

        if ($request->amount > $available) {
            return back()->with('error', 'Refund exceeds available paid amount');
        }

        Refund::create([
            'order_id' => $order->id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Refund request created');
    }

    // =========================
    // APPROVE REFUND
    // =========================
   public function approve($id)
{
    $refund = Refund::findOrFail($id);
    $order = $refund->order;

    // 1. Update refund status
    $refund->status = 'approved';
    $refund->save();

    // 2. IMPORTANT: Adjust order payment
    $order->paid_amount = $order->paid_amount - $refund->amount;

    // 3. Recalculate due amount
    $order->due_amount = max(0, $order->grand_total - $order->paid_amount);

    // 4. Update payment status
    if ($order->paid_amount <= 0) {
        $order->payment_status = 'pending';
    } elseif ($order->due_amount > 0) {
        $order->payment_status = 'partial';
    } else {
        $order->payment_status = 'paid';
    }

    $order->save();

    return back()->with('success', 'Refund approved successfully');
}

    // =========================
    // REJECT REFUND
    // =========================
    public function reject(Refund $refund)
    {
        $refund->status = 'rejected';
        $refund->save();

        return back()->with('success', 'Refund rejected');
    }

    // =========================
    // COMPLETE REFUND (OPTIONAL)
    // =========================
    public function complete(Refund $refund)
    {
        $refund->status = 'completed';
        $refund->save();

        $order = $refund->order;

        // adjust order financials
        $order->paid_amount -= $refund->amount;
        $order->due_amount = $order->grand_total - $order->paid_amount;

        if ($order->paid_amount <= 0) {
            $order->payment_status = 'pending';
        } elseif ($order->paid_amount < $order->grand_total) {
            $order->payment_status = 'partial';
        }

        $order->save();

        return back()->with('success', 'Refund completed and order updated');
    }
}
