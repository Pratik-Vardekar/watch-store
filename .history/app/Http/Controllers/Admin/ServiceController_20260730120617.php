<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('images')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.service.index', compact('services'));
    }

    public function create()
    {
        return view('admin.service.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        $slug = $this->uniqueSlug($slug);

        $sortOrder = $request->filled('sort_order')
            ? (int) $request->sort_order
            : ((int) Service::max('sort_order') + 1);

        $service = Service::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'status' => (int) $request->status,
            'sort_order' => $sortOrder,
        ]);

        $this->storeImages($request, $service);

        return redirect()
            ->route('services.index')
            ->with('success', 'Service added successfully.');
    }

    public function edit(Service $service)
    {
        $service->load('images');

        return view('admin.service.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        $slug = $this->uniqueSlug($slug, $service->id);

        $service->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'status' => (int) $request->status,
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $this->storeImages($request, $service);

        return redirect()
            ->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        foreach ($service->images as $image) {
            $this->deleteImageFile($image->image);
            $image->delete();
        }

        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    public function destroyImage(Service $service, ServiceImage $image)
    {
        if ((int) $image->service_id !== (int) $service->id) {
            abort(404);
        }

        $this->deleteImageFile($image->image);
        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:services,id',
            'orders.*.position' => 'required|integer|min:1',
        ]);

        foreach ($request->orders as $item) {
            Service::where('id', $item['id'])->update([
                'sort_order' => $item['position'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service order updated successfully',
        ]);
    }

    private function storeImages(Request $request, Service $service): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sort = (int) $service->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $sort++;
            $imageName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('backend/assets/images/services'), $imageName);

            ServiceImage::create([
                'service_id' => $service->id,
                'image' => $imageName,
                'sort_order' => $sort,
            ]);
        }
    }

    private function deleteImageFile(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path('backend/assets/images/services/' . $filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug !== '' ? $slug : 'service';
        $candidate = $base;
        $i = 1;

        while (
            Service::where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }
}
