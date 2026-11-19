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
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
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

            $this->markImageChanged($data);
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
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $validated['title'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ];

        /*
         * Only replace image when a new image is selected.
         */
        if ($request->hasFile('image')) {

            // Delete old image
            $this->deleteImage($homeHighlight->image);

            // Upload new image
            $data['image'] = $this->uploadImage(
                $request->file('image')
            );

            // Update timestamp
            $this->markImageChanged($data);
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

    /**
     * Upload image.
     */
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

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (is_file($fullPath)) {
            @chmod($fullPath, 0644);
        }

        return 'backend/assets/images/home-highlights/' . $filename;
    }

    /**
     * Delete old image.
     */
    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = trim($image);

        // Do not try deleting remote URLs
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

        $basename = basename($image);

        if (!$basename) {
            return;
        }

        $path = public_path(
            'backend/assets/images/home-highlights/' . $basename
        );

        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Mark image modification time.
     */
    private function markImageChanged(array &$data): void
    {
        if (Schema::hasColumn(
            'home_highlights',
            'image_updated_at'
        )) {
            $data['image_updated_at'] = now();
        }
    }
}