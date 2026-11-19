<?php
// app/Http/Controllers/Admin/WarrantyGuaranteeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyGuarantee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WarrantyGuaranteeController extends Controller
{
    public function index()
    {
        $warranty = WarrantyGuarantee::orderBy('sort_order')->first();
    
        if (!$warranty) {
            return redirect()
                ->route('warranty-guarantee.create')
                ->with('error', 'No Warranty & Guarantee page found. Please create one first.');
        }
    
        return view('admin.warranty-guarantee.edit', compact('warranty'));
    }
    public function create()
    {
        return view('admin.warranty-guarantee.create');
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

        WarrantyGuarantee::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_active' => $request->has('is_active'),
            'sort_order' => WarrantyGuarantee::max('sort_order') + 1
        ]);

        return redirect()->route('warranty-guarantee.index')
            ->with('success', 'Warranty & Guarantee page created successfully.');
    }

    public function edit(WarrantyGuarantee $warranty)
    {
        return view('admin.warranty-guarantee.edit', compact('warranty'));
    }

    public function update(Request $request, WarrantyGuarantee $warranty)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $warranty->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('warranty-guarantee.edit', 1)
            ->with('success', 'Warranty & Guarantee page updated successfully.');
    }

    public function destroy($id)
    {
        $warranty = WarrantyGuarantee::findOrFail($id);
        $warranty->delete();

        return redirect()->route('warranty-guarantee.index')
        ->with('success', 'Warranty & Guarantee page deleted successfully.');
    }

    public function toggleStatus(WarrantyGuarantee $warranty)
    {
        $warranty->update(['is_active' => !$warranty->is_active]);
        $status = $warranty->is_active ? 'activated' : 'deactivated';
        return redirect()->route('warranty-guarantee.edit', 1)
            ->with('success', "Warranty & Guarantee page has been {$status} successfully.");
    }
}