<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    public function index()
    {
        HomeBanner::ensureDefaults();

        $baseDir = base_path('uploads/home-banners');
        $publicDir = public_path('uploads/home-banners');
        foreach ([$baseDir, $publicDir] as $dir) {
            if (! file_exists($dir)) {
                @mkdir($dir, 0755, true);
                @chmod($dir, 0755);
            }
        }

        $banners = HomeBanner::orderBy('sort_order')->get();

        return view('admin.home-banner.index', compact('banners'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_ids'   => 'required|array',
            'banner_ids.*' => 'integer|exists:home_banners,id',
            'image'        => 'nullable|array',
            'image.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $baseDir = base_path('uploads/home-banners');
        $publicDir = public_path('uploads/home-banners');

        foreach ([$baseDir, $publicDir] as $dir) {
            if (! file_exists($dir)) {
                @mkdir($dir, 0755, true);
                @chmod($dir, 0755);
            }
        }

        foreach ($request->banner_ids as $id) {
            $banner = HomeBanner::find($id);
            if (! $banner) {
                continue;
            }

            if ($request->hasFile("image.$id")) {
                // Delete previous custom image if exists
                if ($banner->image) {
                    $cleanOld = ltrim(str_replace('\\', '/', $banner->image), '/');
                    $oldBase = base_path($cleanOld);
                    $oldPublic = public_path($cleanOld);
                    if (is_file($oldBase)) {
                        @unlink($oldBase);
                    }
                    if (is_file($oldPublic)) {
                        @unlink($oldPublic);
                    }
                }

                $image = $request->file("image.$id");
                $extension = strtolower($image->getClientOriginalExtension() ?: $image->guessExtension() ?: 'jpg');
                $filename = time() . '_' . $banner->slug . '_' . $id . '.' . $extension;

                // Move file to baseDir
                $image->move($baseDir, $filename);
                @chmod($baseDir . '/' . $filename, 0644);

                // Mirror file to publicDir if different directory
                if ($publicDir !== $baseDir) {
                    @copy($baseDir . '/' . $filename, $publicDir . '/' . $filename);
                    @chmod($publicDir . '/' . $filename, 0644);
                }

                $banner->image = 'uploads/home-banners/' . $filename;
                $banner->save();
            }

            if ($request->boolean("remove_image.$id")) {
                if ($banner->image) {
                    $cleanOld = ltrim(str_replace('\\', '/', $banner->image), '/');
                    $oldBase = base_path($cleanOld);
                    $oldPublic = public_path($cleanOld);
                    if (is_file($oldBase)) {
                        @unlink($oldBase);
                    }
                    if (is_file($oldPublic)) {
                        @unlink($oldPublic);
                    }
                }
                $banner->image = null;
                $banner->save();
            }
        }

        return redirect()->back()
            ->with('success', 'Page and listing banners updated successfully.');
    }
}