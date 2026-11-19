<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeServiceItem;
use App\Models\HomeServiceSectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeServiceItemController extends Controller
{
    private array $bannerImageFields = [
        'background_image',
        'watch_image_1',
        'watch_image_2',
    ];

    public function index()
    {
        $items = HomeServiceItem::orderBy('sort_order')->orderBy('id')->get();
        $sectionSetting = $this->sectionSetting();

        return view('admin.home-service-items.index', compact('items', 'sectionSetting'));
    }

    public function updateImages(Request $request)
    {
        $itemIds = HomeServiceItem::pluck('id')->all();

        $rules = [
            'background_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'watch_image_1' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'watch_image_2' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ];

        foreach ($itemIds as $id) {
            $rules["item_image.$id"] = 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120';
            $rules["item_title.$id"] = 'required|string|max:255';
            $rules["item_description.$id"] = 'nullable|string|max:1000';
        }

        $request->validate($rules);

        $sectionSetting = $this->sectionSetting();
        $bannerData = [];

        foreach ($this->bannerImageFields as $field) {
            if ($request->hasFile($field)) {
                $bannerData[$field] = $this->uploadImage(
                    $request->file($field),
                    $sectionSetting->{$field},
                    'banner_' . $field
                );
            }
        }

        if (!empty($bannerData) && $sectionSetting->exists) {
            $sectionSetting->update($bannerData);
        }

        foreach ($itemIds as $id) {
            $item = HomeServiceItem::find($id);
            if (!$item) {
                continue;
            }

            $data = [
                'title' => $request->input("item_title.$id", $item->title),
                'description' => $request->input("item_description.$id", $item->description),
            ];

            if ($request->hasFile("item_image.$id")) {
                $data['image'] = $this->uploadImage(
                    $request->file("item_image.$id"),
                    $item->image,
                    'item_' . $id
                );
                $this->markImageChanged($data);
            }

            $item->update($data);
        }

        return redirect()
            ->route('admin.home-service-items.index')
            ->with('success', 'Service center updated successfully.');
    }

    private function markImageChanged(array &$data): void
    {
        if (Schema::hasColumn('home_service_items', 'image_updated_at')) {
            $data['image_updated_at'] = now();
        }
    }

    private function uploadImage($file, ?string $oldPath, string $prefix): string
    {
        $dir = base_path('frontend/assets/images/home-service-items');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->deleteImage($oldPath);

        $name = time() . '_' . $prefix . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'frontend/assets/images/home-service-items/' . $name;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = ltrim(str_replace('\\', '/', $image), '/');

        if (!str_contains($image, 'home-service-items/')) {
            return;
        }

        $path = base_path($image);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function sectionSetting(): HomeServiceSectionSetting
    {
        if (!Schema::hasTable('home_service_section_settings')) {
            return new HomeServiceSectionSetting();
        }

        return HomeServiceSectionSetting::firstOrCreate([]);
    }
}
