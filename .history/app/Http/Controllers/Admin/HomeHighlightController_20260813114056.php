<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;

class HomeHighlightController extends Controller
{
    public function index()
    {
        $highlights = HomeHighlight::orderBy('sort_order')->orderBy('id')->get();

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
            'icon' => 'required|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->uploadIcon($request->file('icon'));
        }

        HomeHighlight::create($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight added successfully.');
    }

    public function edit(HomeHighlight $homeHighlight)
    {
        return view('admin.home-highlights.edit', compact('homeHighlight'));
    }

    public function update(Request $request, HomeHighlight $homeHighlight)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'title' => $request->title,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        if ($request->hasFile('icon')) {
            $this->deleteIcon($homeHighlight->icon);
            $data['icon'] = $this->uploadIcon($request->file('icon'));
        }

        $homeHighlight->update($data);

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight updated successfully.');
    }

    public function destroy(HomeHighlight $homeHighlight)
    {
        $this->deleteIcon($homeHighlight->icon);
        $homeHighlight->delete();

        return redirect()
            ->route('admin.home-highlights.index')
            ->with('success', 'Highlight deleted successfully.');
    }

    private function uploadIcon($file): string
    {
        $dir = public_path('frontend/assets/images/home-highlights');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = time() . '_' . uniqid() . '_highlight.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'frontend/assets/images/home-highlights/' . $name;
    }

    private function deleteIcon(?string $icon): void
    {
        if (!$icon) {
            return;
        }

        $icon = ltrim(str_replace('\\', '/', $icon), '/');
        $paths = [
            public_path($icon),
            public_path('frontend/assets/images/home-highlights/' . basename($icon)),
            public_path('backend/assets/images/home-highlights/' . basename($icon)),
        ];

        foreach (array_unique($paths) as $path) {
            if (is_file($path) && str_contains($path, 'home-highlights')) {
                @unlink($path);
            }
        }
    }
}
