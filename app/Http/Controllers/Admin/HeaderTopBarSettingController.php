<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderTopBarSetting;
use Illuminate\Http\Request;

class HeaderTopBarSettingController extends Controller
{
    public function edit()
    {
        $setting = HeaderTopBarSetting::firstOrCreate([], [
            'delivery_message' => 'Get free home delivery (Order more than ₹300)',
            'email' => 'cheriksinnovativeventures@gmail.com',
            'address' => 'Santacruz West, Mumbai 400 054',
        ]);

        return view('admin.header-top-bar-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'delivery_message' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $setting = HeaderTopBarSetting::firstOrCreate([]);

        $setting->update($request->only([
            'delivery_message',
            'email',
            'address',
        ]));

        return back()->with('success', 'Header top bar settings updated successfully.');
    }
}
