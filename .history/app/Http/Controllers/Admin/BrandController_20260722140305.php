<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'logo'     => 'required|image|mimes:jpg,jpeg,png,webp',
            'link'     => 'nullable',
            'position' => 'nullable|integer',
            'status'   => 'required|boolean',
        ]);

        $logo = '';

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');
            $logo = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/images/brands'), $logo);
        }

        Brand::create([
            'name'     => $request->name,
            'logo'     => $logo,
            'link'     => $request->link,
            'position' => $request->position ?? 0,
            'status'   => $request->status,
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
            'name'     => 'required|string|max:255',
            'logo'     => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'link'     => 'nullable',
            'position' => 'nullable|integer',
            'status'   => 'required|boolean',
        ]);

        $logo = $brand->logo;

        if ($request->hasFile('logo')) {

            if ($brand->logo && file_exists(public_path('backend/assets/images/brands/' . $brand->logo))) {
                unlink(public_path('backend/assets/images/brands/' . $brand->logo));
            }

            $file = $request->file('logo');
            $logo = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/images/brands'), $logo);
        }

        $brand->update([
            'name'     => $request->name,
            'logo'     => $logo,
            'link'     => $request->link,
            'position' => $request->position ?? 0,
            'status'   => $request->status,
        ]);

        return redirect()->route('brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo && file_exists(public_path('backend/assets/images/brands/' . $brand->logo))) {
            unlink(public_path('backend/assets/images/brands/' . $brand->logo));
        }

        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }


}
