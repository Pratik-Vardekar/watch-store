<?php
// app/Http/Controllers/Admin/StoreController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('sort_order')->get();
        return view('frontend.pages.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'timing' => 'nullable|string|max:255',
            'google_maps_link' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    
        $data = [
            'name' => $request->name,
            'slug' => $request->slug,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'timing' => $request->timing,
            'google_maps_link' => $request->google_maps_link,
            'latitude' => $request->latitude ?: null,
            'longitude' => $request->longitude ?: null,
            'images' => $request->images,
            'is_active' => $request->is_active,
            'sort_order' => $request->sort_order ?? 0,
        ];
    
        Store::create($data);
    
        return redirect()
            ->route('admin.stores.index')
            ->with('success', 'Store added successfully.');
    }
    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'timing' => 'required|string|max:255',
            'google_maps_link' => 'nullable|url',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean'
        ];

        $request->validate($rules);

        $images = $store->images ?? [];

        $images = array_values(array_merge($images, $this->storeUploadedImages($request->file('images', []))));

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

        $this->deleteStoredImages($store->images ?? []);

        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    public function deleteImage(Request $request, Store $store)
    {
        $imageIndex = $request->image_index;
        $images = $store->images ?? [];

        if (isset($images[$imageIndex])) {
            $image = $images[$imageIndex];

            $this->deleteStoredImages([$image]);

            unset($images[$imageIndex]);
            $images = array_values($images);
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

    /**
     * Store uploaded store images and return the saved filenames.
     */
    private function storeUploadedImages(array $files): array
    {
        $images = [];
        $directory = public_path('backend/assets/images/stores');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);
            $images[] = $filename;
        }

        return $images;
    }

    /**
     * Remove one or more previously stored store images from disk.
     */
    private function deleteStoredImages(array $images): void
    {
        $directory = public_path('backend/assets/images/stores');

        foreach ($images as $image) {
            if (! $image) {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . basename($image);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}