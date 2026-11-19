<?php
// app/Http/Controllers/Admin/ReturnRefundController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReturnRefundController extends Controller
{
    public function index()
    {
        $returnRefunds = ReturnRefund::orderBy('sort_order')->get();
        return view('admin.return-refunds.index', compact('returnRefunds'));
    }

    public function create()
    {
        return view('admin.return-refunds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        ReturnRefund::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_active' => $request->has('is_active'),
            'sort_order' => ReturnRefund::max('sort_order') + 1
        ]);

        return redirect()->route('admin.return-refunds.index')
            ->with('success', 'Return & Refund page created successfully.');
    }

    public function edit(ReturnRefund $returnRefund)
    {
        return view('admin.return-refunds.edit', compact('returnRefund'));
    }

    public function update(Request $request, ReturnRefund $returnRefund)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $returnRefund->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('return-refund.index')
            ->with('success', 'Return & Refund page updated successfully.');
    }

    public function destroy($id)
    {
        $returnRefund = ReturnRefund::findOrFail($id);
        $returnRefund->delete();

        return redirect()->route('return-refunds.index')
            ->with('success', 'Return & Refund page deleted successfully.');
    }

    public function toggleStatus(ReturnRefund $returnRefund)
    {
        $returnRefund->update(['is_active' => !$returnRefund->is_active]);
        $status = $returnRefund->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.return-refunds.index')
            ->with('success', "Return & Refund page has been {$status} successfully.");
    }
}