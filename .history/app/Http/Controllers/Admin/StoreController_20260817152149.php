<?php

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
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'timing' => 'required|string|max:255',
            'google_maps_link' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // Changed to single image
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Store single image
        $image = $this->storeUploadedImage($request->file('image'));

        Store::create([
            'name' => $request->name,
            'slug' => $this->generateUniqueSlug($request->name),
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'timing' => $request->timing,
            'google_maps_link' => $request->google_maps_link,
            'latitude' => $request->latitude ?: null,
            'longitude' => $request->longitude ?: null,
            'images' => $image ? [$image] : [], // Store as array with single image
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? ((int) Store::max('sort_order') + 1),
        ]);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store added successfully.');
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
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'timing' => 'required|string|max:255',
            'google_maps_link' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120', // Changed to single image
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $slug = $store->slug;
        if ($request->name !== $store->name) {
            $slug = $this->generateUniqueSlug($request->name, $store->id);
        }

        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'timing' => $request->timing,
            'google_maps_link' => $request->google_maps_link,
            'latitude' => $request->latitude ?: null,
            'longitude' => $request->longitude ?: null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? $store->sort_order,
        ];

        // If new image uploaded, delete old and save new
        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteStoredImages($store->image_filenames);
            
            // Store new image
            $image = $this->storeUploadedImage($request->file('image'));
            $data['images'] = $image ? [$image] : [];
        }

        $store->update($data);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);

        $this->deleteStoredImages($store->image_filenames);

        $store->delete();

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    public function deleteImage(Request $request, Store $store)
    {
        // Delete the single image
        $this->deleteStoredImages($store->image_filenames);
        $store->update(['images' => []]);

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }

    public function toggleStatus(Store $store)
    {
        $store->update(['is_active' => ! $store->is_active]);
        $status = $store->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('stores.index')
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
     * Store a single uploaded store image and return the saved filename.
     */
    private function storeUploadedImage($file): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $directory = public_path('uploads/stores');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Unable to create uploads/stores folder.');
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $filename = time() . '_' . uniqid() . '_store.' . $extension;
        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
        if (is_file($fullPath)) {
            @chmod($fullPath, 0644);
        }

        return $filename;
    }

    /**
     * Remove one or more previously stored store images from disk.
     */
    private function deleteStoredImages(array $images): void
    {
        $directory = public_path('uploads/stores');

        foreach ($images as $image) {
            if (! $image) {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . basename($image);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}