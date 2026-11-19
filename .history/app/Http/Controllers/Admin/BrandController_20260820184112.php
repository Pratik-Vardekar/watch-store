<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::ordered()->get();
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'logo'        => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|url|max:512',
            'status'      => 'required|boolean',
        ]);

        $logo = '';

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logo = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(base_path('backend/assets/images/brands'), $logo);
        }

        $nextPosition = (int) Brand::max('position') + 1;

        Brand::create([
            'name'        => $request->name,
            'logo'        => $logo,
            'description' => $request->description,
            'link'        => $request->link,
            'position'    => $nextPosition,
            'status'      => $request->status,
        ]);

        return redirect()->route('brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|url|max:512',
            'status'      => 'required|boolean',
        ]);

        $logo = $brand->logo;

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo && file_exists(base_path('backend/assets/images/brands/' . $brand->logo))) {
                @unlink(base_path('backend/assets/images/brands/' . $brand->logo));
            }

            $file = $request->file('logo');
            $logo = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(base_path('backend/assets/images/brands'), $logo);
        }

        $brand->update([
            'name'        => $request->name,
            'logo'        => $logo,
            'description' => $request->description,
            'link'        => $request->link,
            'status'      => $request->status,
        ]);

        return redirect()->route('brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        // Delete logo file
        if ($brand->logo && file_exists(base_path('backend/assets/images/brands/' . $brand->logo))) {
            @unlink(base_path('backend/assets/images/brands/' . $brand->logo));
        }

        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders'            => 'required|array',
            'orders.*.id'       => 'required|integer|exists:brands,id',
            'orders.*.position' => 'required|integer|min:1',
        ]);

        foreach ($request->orders as $item) {
            Brand::where('id', $item['id'])->update([
                'position' => $item['position'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Brand order updated successfully',
        ]);
    }
}