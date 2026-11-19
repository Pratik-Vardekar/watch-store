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
            $data['image'] = $this->uploadImage($request->file('image'));
            $data['image_updated_at'] = now();
        }

        HomeHighlight::create($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight added successfully.');
    }

    // ✅ Use snake_case parameter name to match Route::resource()
    public function edit(HomeHighlight $home_highlight)
    {
        return view('admin.home-highlights.edit', compact('home_highlight'));
    }

    // ✅ Use snake_case parameter name to match Route::resource()
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
            $this->deleteImage($home_highlight->image);
            $data['image'] = $this->uploadImage($request->file('image'));
            $data['image_updated_at'] = now(); // ✅ Update timestamp
        }

        $home_highlight->update($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    // ✅ Use snake_case parameter name to match Route::resource()
    public function destroy(HomeHighlight $home_highlight)
    {
        $this->deleteImage($home_highlight->image);
        $home_highlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight deleted successfully.');
    }

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

        return 'backend/assets/images/home-highlights/' . $filename;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $image = trim($image);

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