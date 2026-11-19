<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;

class ShippingSettingController extends Controller
{
    public function index()
    {
        $setting = ShippingSetting::first();

        return view('admin.shipping.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'free_shipping_amount' => 'required|numeric|min:0',
            'shipping_charge' => 'required|numeric|min:0',
        ]);

        ShippingSetting::updateOrCreate(
            ['id' => 1],
            [
                'free_shipping_amount' => $request->free_shipping_amount,
                'shipping_charge' => $request->shipping_charge,
                'status' => 1,
            ]
        );

        return back()->with('success', 'Shipping settings updated successfully.');
    }
}