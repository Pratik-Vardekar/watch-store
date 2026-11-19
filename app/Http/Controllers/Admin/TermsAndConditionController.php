<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
    public function index()
    {
        $terms = TermsAndCondition::firstOrCreate([]);

        return view('admin.terms-and-conditions.index', compact('terms'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
        ]);

        $terms = TermsAndCondition::firstOrCreate([]);
        $terms->update(['content' => $request->content]);

        return back()->with('success', 'Terms and Conditions updated successfully.');
    }
}
