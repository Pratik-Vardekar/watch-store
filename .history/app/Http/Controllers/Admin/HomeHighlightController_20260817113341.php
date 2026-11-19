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
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage(
                $request->file('image')
            );
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
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            $this->deleteImage($homeHighlight->image);

            // Upload new image
            $data['image'] = $this->uploadImage(
                $request->file('image')
            );
        }

        $homeHighlight->update($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    public function destroy(HomeHighlight $homeHighlight)
    {
        // Delete image from public folder
        $this->deleteImage($homeHighlight->image);

        // Delete database record
        $homeHighlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight deleted successfully.');
    }

    private function uploadImage($file): string
    {
        $dir = public_path(
            'frontend/assets/images/home-highlights'
        );

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $name = time()
            . '_'
            . uniqid()
            . '_highlight.'
            . $extension;

        $file->move($dir, $name);

        return 'frontend/assets/images/home-highlights/' . $name;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = ltrim(
            str_replace('\\', '/', $image),
            '/'
        );

        // Only delete files from home-highlights folder
        if (!str_contains($image, 'home-highlights/')) {
            return;
        }

        $paths = [
            public_path($image),
            public_path(
                'frontend/assets/images/home-highlights/'
                . basename($image)
            ),
            public_path(
                'backend/assets/images/home-highlights/'
                . basename($image)
            ),
        ];

        foreach (array_unique($paths) as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}