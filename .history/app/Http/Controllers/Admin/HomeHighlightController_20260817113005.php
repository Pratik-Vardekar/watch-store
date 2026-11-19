<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HomeHighlightController extends Controller
{
    private const ALLOWED_ICON_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

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
            'icon' => 'required|file|extensions:jpg,jpeg,png,webp,gif,svg|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $file = $this->uploadedIcon($request, true);

        $data = [
            'title' => $request->title,
            'icon' => $this->uploadIcon($file),
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

        $this->markImageChanged($data);

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
        $file = $this->uploadedIcon($request, false);

        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|file|extensions:jpg,jpeg,png,webp,gif,svg|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $homeHighlight->title = $request->title;
        $homeHighlight->sort_order = $request->sort_order ?? 0;
        $homeHighlight->status = $request->status;

        if ($file) {
            $oldIcon = $homeHighlight->icon;
            $homeHighlight->icon = $this->uploadIcon($file);
            $this->markImageChangedOnModel($homeHighlight);
            $homeHighlight->save();
            $this->deleteIcon($oldIcon);
        } else {
            $homeHighlight->save();
        }

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

    private function uploadedIcon(Request $request, bool $required = false): ?UploadedFile
    {
        $file = $request->file('icon') ?? $request->files->get('icon');

        if (!$file instanceof UploadedFile || $file->getError() === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                throw ValidationException::withMessages([
                    'icon' => 'Please upload an image.',
                ]);
            }

            return null;
        }

        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                'icon' => $file->getErrorMessage() ?: 'The image failed to upload. Please try a smaller file.',
            ]);
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_ICON_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'icon' => 'The image must be a JPG, PNG, WEBP, GIF or SVG file.',
            ]);
        }

        return $file;
    }

    private function markImageChanged(array &$data): void
    {
        if (Schema::hasColumn('home_highlights', 'image_updated_at')) {
            $data['image_updated_at'] = now();
        }
    }

    private function markImageChangedOnModel(HomeHighlight $homeHighlight): void
    {
        if (Schema::hasColumn('home_highlights', 'image_updated_at')) {
            $homeHighlight->image_updated_at = now();
        }
    }

    private function uploadIcon(UploadedFile $file): string
    {
        $dir = public_path('frontend/assets/images/home-highlights');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $name = time() . '_' . uniqid() . '_highlight.' . $ext;
        $file->move($dir, $name);

        $relative = 'frontend/assets/images/home-highlights/' . $name;

        if (!is_file(public_path($relative))) {
            throw ValidationException::withMessages([
                'icon' => 'The image could not be saved. Please try again.',
            ]);
        }

        return $relative;
    }

    private function deleteIcon(?string $icon): void
    {
        if (!$icon) {
            return;
        }

        $icon = ltrim(str_replace('\\', '/', $icon), '/');

        if (!str_contains($icon, 'home-highlights/')) {
            return;
        }

        $path = public_path($icon);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
