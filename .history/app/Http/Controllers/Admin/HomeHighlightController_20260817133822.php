<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeHighlightController extends Controller
{
    public function index()
    {
        HomeHighlight::ensureDefaults();

        $highlights = HomeHighlight::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.home-highlights.index', compact('highlights'));
    }

    public function edit(HomeHighlight $homeHighlight)
    {
        return view('admin.home-highlights.edit', compact('homeHighlight'));
    }

    public function updateCard(Request $request, HomeHighlight $homeHighlight)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $this->saveUploadedImage($homeHighlight, $request->file('image'));

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Card image updated successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'highlight_ids' => 'required|array',
            'highlight_ids.*' => 'integer|exists:home_highlights,id',
            'image' => 'nullable|array',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $updated = 0;

        foreach ($request->highlight_ids as $id) {
            $highlight = HomeHighlight::find($id);

            if (! $highlight || ! $request->hasFile("image.$id")) {
                continue;
            }

            $this->saveUploadedImage($highlight, $request->file("image.$id"));
            $updated++;
        }

        return redirect()
            ->route('admin.home-highlights.index')
            ->with(
                'success',
                $updated > 0
                    ? $updated . ' card image(s) updated successfully.'
                    : 'No new image was selected. Choose an image and save again.'
            );
    }

    private function saveUploadedImage(HomeHighlight $highlight, $file): void
    {
        // Delete old image
        $this->deleteImage($highlight->storedImagePath());

        // Upload new image
        $path = $this->uploadImage($file);
        $column = HomeHighlight::imageColumn();

        $data = [
            $column => $path,
        ];

        if (Schema::hasColumn('home_highlights', 'image_updated_at')) {
            $data['image_updated_at'] = now();
        }

        $highlight->forceFill($data);
        $highlight->updated_at = now();
        $highlight->save();
    }

    private function uploadImage($file): string
    {
        // Save to public/uploads/home-highlights
        $directory = base_path('uploads/home-highlights');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Unable to create uploads/home-highlights folder.');
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $filename = time() . '_' . uniqid() . '_highlight.' . $extension;

        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($fullPath)) {
            throw new \RuntimeException('Image upload failed. Please try again.');
        }

        @chmod($fullPath, 0644);

        // Return path relative to public directory
        return 'uploads/home-highlights/' . $filename;
    }

    private function deleteImage(?string $image): void
    {
        if (! $image) {
            return;
        }

        $image = ltrim(str_replace('\\', '/', trim($image)), '/');

        if (
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://') ||
            ! str_contains($image, 'home-highlights/')
        ) {
            return;
        }

        // Delete from public directory
        $path = base_path($image);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}