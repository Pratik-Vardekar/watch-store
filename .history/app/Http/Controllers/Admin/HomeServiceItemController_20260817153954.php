<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeServiceItemController extends Controller
{
    public function index()
    {
        $items = HomeServiceItem::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.home-service-items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.home-service-items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
            $this->markImageChanged($data);
        }

        HomeServiceItem::create($data);

        return redirect()
            ->route('admin.home-service-items.index')
            ->with('success', 'Service item added successfully.');
    }

    public function edit(HomeServiceItem $homeServiceItem)
    {
        return view('admin.home-service-items.edit', compact('homeServiceItem'));
    }

    public function update(Request $request, HomeServiceItem $homeServiceItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($homeServiceItem->image);
            $data['image'] = $this->uploadImage($request->file('image'));
            $this->markImageChanged($data);
        }

        $homeServiceItem->update($data);

        return redirect()
            ->route('admin.home-service-items.index')
            ->with('success', 'Service item updated successfully.');
    }

    public function destroy(HomeServiceItem $homeServiceItem)
    {
        $this->deleteImage($homeServiceItem->image);
        $homeServiceItem->delete();

        return redirect()
            ->route('admin.home-service-items.index')
            ->with('success', 'Service item deleted successfully.');
    }

    private function markImageChanged(array &$data): void
    {
        if (Schema::hasColumn('home_service_items', 'image_updated_at')) {
            $data['image_updated_at'] = now();
        }
    }

    private function uploadImage($file): string
    {
        $dir = base_path('frontend/assets/images/home-service-items');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = time() . '_' . uniqid() . '_service.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'frontend/assets/images/home-service-items/' . $name;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = ltrim(str_replace('\\', '/', $image), '/');

        // Keep seeded default images under frontend/assets/images root
        if (!str_contains($image, 'home-service-items/')) {
            return;
        }

        $path = base_path($image);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
