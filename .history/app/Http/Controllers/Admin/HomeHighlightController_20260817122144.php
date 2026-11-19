<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;

class HomeHighlightController extends Controller
{
    public function index()
    {
        $highlights = HomeHighlight::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.home-highlights.index', compact('highlights'));
    }

    public function create()
    {
        return view('admin.home-highlights.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp,gif,svg',
                'max:5120',
            ],
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $validated['title'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ];

        if ($request->hasFile('image')) {
            // ✅ Upload and get the stored path
            $data['image'] = $this->uploadImage($request->file('image'));
            $data['image_updated_at'] = now();
        }

        $highlight = HomeHighlight::create($data);

        // ✅ Debug: Check what was saved
        // \Log::info('HomeHighlight created:', ['image' => $highlight->image]);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight added successfully.');
    }

    public function edit(HomeHighlight $home_highlight)
    {
        return view('admin.home-highlights.edit', compact('home_highlight'));
    }

    public function update(Request $request, HomeHighlight $home_highlight)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp,gif,svg',
                'max:5120',
            ],
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $validated['title'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteImage($home_highlight->image);
            
            // Upload new image
            $data['image'] = $this->uploadImage($request->file('image'));
            $data['image_updated_at'] = now();
        }

        $home_highlight->update($data);

        // ✅ Debug: Check what was updated
        // \Log::info('HomeHighlight updated:', ['image' => $home_highlight->fresh()->image]);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    public function destroy(HomeHighlight $home_highlight)
    {
        $this->deleteImage($home_highlight->image);
        $home_highlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight deleted successfully.');
    }

    /**
     * Upload image to the server
     */
    private function uploadImage($file): string
    {
        $directory = public_path('backend/assets/images/home-highlights');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
            ?: $file->guessExtension()
            ?: 'jpg'
        );

        $filename = time() . '_' . uniqid() . '_highlight.' . $extension;
        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
        if (is_file($fullPath)) {
            chmod($fullPath, 0644);
        }

        // ✅ Return the relative path (not full URL)
        return 'backend/assets/images/home-highlights/' . $filename;
    }

    /**
     * Delete image from server
     */
    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = trim($image);

        // Don't delete external URLs
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return;
        }

        $image = str_replace('\\', '/', $image);
        $image = ltrim($image, '/');

        if (str_starts_with($image, 'public/')) {
            $image = substr($image, 7);
        }

        $filename = basename($image);
        $path = public_path('backend/assets/images/home-highlights/' . $filename);

        if (is_file($path)) {
            unlink($path);
        }
    }
}