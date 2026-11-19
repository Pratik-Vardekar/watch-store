<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
        $path = $file->store('home-highlights', 'public');
        
        if (!$path) {
            throw new \RuntimeException('Image upload failed. Please try again.');
        }

        return $path;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        if (
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://')
        ) {
            return;
        }

        if (Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }
    }
}