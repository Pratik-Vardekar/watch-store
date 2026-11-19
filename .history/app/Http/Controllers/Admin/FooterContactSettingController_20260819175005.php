<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterContactSetting;
use Illuminate\Http\Request;

class FooterContactSettingController extends Controller
{
    private array $imageFields = [
        'foot_kids_image',
        'foot_kids_shadow_image',
        'enroll_ribbon_image',
        'enroll_product_image',
        'connect_tag_image',
        'amazon_logo_image',
    ];

    public function edit()
    {
        $setting = FooterContactSetting::firstOrCrea    te([], [
            'contact_phone' => '+91 98922 51015',
            'contact_email' => 'watchinnovativeventures@gmail.com',
            'contact_address' => 'D-201, AcharatBaug, Phirozshah Mehta Road, Santacruz West, Mumbai - 400054.',
            'form_title' => 'Reach out to us!',
            'form_subtitle' => "Start your child's journey of fun-filled learning with us",
            'enroll_title_small' => 'Enroll Your Child in',
            'enroll_title_highlight' => 'The Word Hunt',
            'enroll_title_suffix' => 'Challenge Today!',
            'enroll_button_text' => 'Enroll Now',
            'enroll_button_url' => '#',
            'youtube_url' => 'https://www.youtube.com/@WatchInnvovativeVentures',
            'copyright_text' => '© 2026 Watch. All rights reserved.',
        ]);

        return view('admin.footer-contact-settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string',
            'form_title' => 'nullable|string|max:255',
            'form_subtitle' => 'nullable|string|max:500',
            'enroll_title_small' => 'nullable|string|max:255',
            'enroll_title_highlight' => 'nullable|string|max:255',
            'enroll_title_suffix' => 'nullable|string|max:255',
            'enroll_button_text' => 'nullable|string|max:100',
            'enroll_button_url' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'amazon_url' => 'nullable|url|max:500',
            'copyright_text' => 'nullable|string|max:255',
            'foot_kids_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'foot_kids_shadow_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'enroll_ribbon_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'enroll_product_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'connect_tag_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'amazon_logo_image' => 'nullable|file|mimes:png|max:2048',
        ]);

        $setting = FooterContactSetting::firstOrCreate([]);

        $data = $request->only([
            'contact_phone',
            'contact_email',
            'contact_address',
            'form_title',
            'form_subtitle',
            'enroll_title_small',
            'enroll_title_highlight',
            'enroll_title_suffix',
            'enroll_button_text',
            'enroll_button_url',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'youtube_url',
            'amazon_url',
            'copyright_text',
        ]);

        foreach ($this->imageFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->uploadImage($request->file($field), $setting->{$field}, $field);
            }
        }

        $setting->update($data);

        return back()->with('success', 'Footer settings updated successfully.');
    }

    private function uploadImage($file, ?string $oldPath, string $prefix): string
    {
        $dir = public_path('backend/assets/images/footer');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($oldPath && !str_starts_with($oldPath, 'frontend/') && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        $name = time() . '_' . $prefix . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'backend/assets/images/footer/' . $name;
    }
}
