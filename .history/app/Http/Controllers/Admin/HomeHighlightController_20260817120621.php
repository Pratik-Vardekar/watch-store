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

        return view(
            'admin.home-highlights.index',
            compact('highlights')
        );
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
            $data['image'] = $this->uploadImage(
                $request->file('image')
            );

            $data['image_updated_at'] = now();
        }

        HomeHighlight::create($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight added successfully.');
    }

    public function edit(HomeHighlight $homeHighlight)
    {
        return view(
            'admin.home-highlights.edit',
            compact('homeHighlight')
        );
    }

    public function update(
        Request $request,
        HomeHighlight $homeHighlight
    ) {
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

        /*
         * Only replace image if a new image
         * has been selected.
         */
        if ($request->hasFile('image')) {

            // Delete previous image
            $this->deleteImage($homeHighlight->image);

            // Upload new image
            $data['image'] = $this->uploadImage(
                $request->file('image')
            );

            // Update image timestamp
            $data['image_updated_at'] = now();
        }

        $homeHighlight->update($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    public function destroy(HomeHighlight $homeHighlight)
    {
        $this->deleteImage($homeHighlight->image);

        $homeHighlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight deleted successfully.');
    }

    private function uploadImage($file): string
    {
        $directory = public_path(
            'backend/assets/images/home-highlights'
        );

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
            ?: $file->guessExtension()
            ?: 'jpg'
        );

        $filename =
            time() .
            '_' .
            uniqid() .
            '_highlight.' .
            $extension;

        $file->move($directory, $filename);

        $fullPath =
            $directory . DIRECTORY_SEPARATOR . $filename;

        if (is_file($fullPath)) {
            chmod($fullPath, 0644);
        }

        return 'backend/assets/images/home-highlights/' . $filename;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = trim($image);

        // Don't delete external URLs
        if (
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://')
        ) {
            return;
        }

        $image = str_replace('\\', '/', $image);
        $image = ltrim($image, '/');

        if (str_starts_with($image, 'public/')) {
            $image = substr($image, 7);
        }

        $filename = basename($image);

        if (!$filename) {
            return;
        }

        $path = public_path(
            'backend/assets/images/home-highlights/' .
            $filename
        );

        if (is_file($path)) {
            unlink($path);
        }
    }
}