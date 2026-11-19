<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderMenuSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HeaderMenuSettingsController extends Controller
{
    private array $imageFields = [
        'brands_image',
        'women_image',
        'men_image',
        'about_image',
    ];

    public function edit()
    {
        $setting = $this->setting();

        return view('admin.header-menu-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brands_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'women_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'men_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ]);

        $setting = $this->setting();

        $data = [];

        foreach ($this->imageFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadImage($request->file($field), $setting->{$field}, $field);
            }
        }

        if (!empty($data) && $setting->exists) {
            $setting->update($data);
        }

        return back()->with('success', 'Header menu images updated successfully.');
    }

    private function setting(): HeaderMenuSetting
    {
        if (!Schema::hasTable('header_menu_settings')) {
            return new HeaderMenuSetting();
        }

        return HeaderMenuSetting::firstOrCreate([], [
            'brands_image' => null,
            'women_image' => null,
            'men_image' => null,
            'about_image' => null,
        ]);
    }

    private function uploadImage($file, ?string $oldPath, string $prefix): string
    {
        $dir = public_path('backend/assets/images/header-menu');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($oldPath && str_starts_with($oldPath, 'backend/assets/images/header-menu/') && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        $name = time() . '_' . $prefix . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'backend/assets/images/header-menu/' . $name;
    }
}

