<?php
// app/Http/Controllers/Admin/StoreController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('sort_order')->get();
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'timing' => 'required|string|max:255',
            'google_maps_link' => 'nullable|url',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    // Store with proper path
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('stores', $filename, 'public');
                    // Store only the filename, not the full path
                    $images[] = 'stores/' . $filename;
                }
            }
        }

        $slug = $this->generateUniqueSlug($request->name);

        Store::create([
            'name' => $request->name,
            'slug' => $slug,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'timing' => $request->timing,
            'google_maps_link' => $request->google_maps_link,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'images' => $images,
            'is_active' => $request->has('is_active'),
            'sort_order' => Store::max('sort_order') + 1
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'timing' => 'required|string|max:255',
            'google_maps_link' => 'nullable|url',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        $images = $store->images ?? [];

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('stores', $filename, 'public');
                    $images[] = 'stores/' . $filename;
                }
            }
        }

        // Generate unique slug if name changed
        $slug = $store->slug;
        if ($request->name !== $store->name) {
            $slug = $this->generateUniqueSlug($request->name, $store->id);
        }

        $store->update([
            'name' => $request->name,
            'slug' => $slug,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'timing' => $request->timing,
            'google_maps_link' => $request->google_maps_link,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'images' => $images,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        
        // Delete all associated images
        if ($store->images) {
            foreach ($store->images as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }
        
        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    public function deleteImage(Request $request, Store $store)
    {
        $imageIndex = $request->image_index;
        $images = $store->images ?? [];

        if (isset($images[$imageIndex])) {
            // Delete file from storage
            if (Storage::disk('public')->exists($images[$imageIndex])) {
                Storage::disk('public')->delete($images[$imageIndex]);
            }
            
            // Remove from array
            unset($images[$imageIndex]);
            $images = array_values($images); // Re-index array
            
            $store->update(['images' => $images]);
            
            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    public function toggleStatus(Store $store)
    {
        $store->update(['is_active' => !$store->is_active]);
        $status = $store->is_active ? 'activated' : 'deactivated';
        return redirect()->route('stores.index')
            ->with('success', "Store has been {$status} successfully.");
    }

    public function reorderStores(Request $request)
    {
        foreach ($request->stores as $index => $storeId) {
            Store::where('id', $storeId)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }

    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    private function slugExists($slug, $excludeId = null)
    {
        $query = Store::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }
}