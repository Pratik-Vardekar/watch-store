<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactSetting;

class ContactSettingController extends Controller
{
    public function edit()
    {
        $setting = ContactSetting::first();

        if (!$setting) {
            $setting = ContactSetting::create([]);
        }

        return view('admin.contact-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'business_days' => 'nullable|string|max:255',
            'business_hours' => 'nullable|string|max:255',
            'support_message' => 'nullable|string',
            'support_timings' => 'nullable|string|max:255',
        ]);

        $setting = ContactSetting::firstOrCreate([]);

        $setting->update($request->only([
            'contact_address',
            'contact_phone',
            'contact_email',
            'business_days',
            'business_hours',
            'support_message',
            'support_timings',
        ]));

        return back()->with('success', 'Contact settings updated successfully.');
    }
}