<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Profile Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('admin.profile.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Profile
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $request->validate([

            'name'  => 'required|max:255',

            'email' => 'required|email|max:255',

            'phone' => 'nullable|max:20',

        ]);

        auth()->user()->update([

            'name'  => $request->name,

            'email' => $request->email,

            'phone' => $request->phone,

        ]);

        return back()->with(
            'success',
            'Profile updated successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */

    public function changePassword(Request $request)
    {
        $request->validate([

            'current_password' => 'required',

            'password' => 'required|min:6|confirmed',

        ]);

        $user = auth()->user();

        if (!Hash::check(
            $request->current_password,
            $user->password
        )) {

            return back()->with(
                'error',
                'Current password is incorrect'
            );
        }

        $user->update([

            'password' => Hash::make($request->password),

            'plain_password' => $request->password,

        ]);

        return back()->with(
            'success',
            'Password changed successfully'
        );
    }

    public function userInvoice($order)
    {
        $order = Order::where('id', $order)
            ->where('user_id', Auth::id())
            ->with([
                'user',
                'items.product',
                'items.variant',
                'orderPayments',
                'refunds',
                'addresses',
            ])
            ->firstOrFail();

        $rawInvoice = $order->getRawOriginal('invoice_no');
        if (empty($rawInvoice) || !preg_match('/^INV\d{3,8}$/', (string) $rawInvoice)) {
            $order->update([
                'invoice_no' => Order::generateNextInvoiceNumber($order->id),
                'invoice_date' => $order->invoice_date ?? now()->toDateString(),
            ]);
            $order->refresh();
        }

        $pdf = Pdf::loadView('admin.order.invoice', compact('order'))
            ->setPaper('a4');

        $filename = 'invoice-' . ($order->invoice_no ?? $order->order_id ?? $order->id) . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
