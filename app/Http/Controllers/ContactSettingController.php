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
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $setting = ContactSetting::first();

        $setting->update($request->all());

        return back()->with('success', 'Contact settings updated successfully.');
    }
}