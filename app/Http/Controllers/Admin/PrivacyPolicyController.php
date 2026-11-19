<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $privacy = PrivacyPolicy::firstOrCreate([]);

        return view('admin.privacy-policy.index', compact('privacy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
        ]);

        $privacy = PrivacyPolicy::firstOrCreate([]);
        $privacy->update(['content' => $request->content]);

        return back()->with('success', 'Privacy Policy updated successfully.');
    }
}
