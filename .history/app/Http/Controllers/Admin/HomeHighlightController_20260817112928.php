<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHighlight;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HomeHighlightController extends Controller
{
    private const ALLOWED_IMAGE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'svg',
    ];

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
            'image' => 'required|file|extensions:jpg,jpeg,png,webp,gif,svg|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $file = $this->uploadedImage($request, true);

        $data = [
            'title' => $request->title,
            'image' => $this->uploadImage($file),
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ];

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
            'image' => 'nullable|file|extensions:jpg,jpeg,png,webp,gif,svg|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $homeHighlight->title = $request->title;
        $homeHighlight->sort_order = $request->sort_order ?? 0;
        $homeHighlight->status = $request->status;

        $file = $this->uploadedImage($request);

        if ($file) {
            $oldImage = $homeHighlight->image;

            $homeHighlight->image = $this->uploadImage($file);

            $homeHighlight->save();

            $this->deleteImage($oldImage);
        } else {
            $homeHighlight->save();
        }

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

    private function uploadedImage(
        Request $request,
        bool $required = false
    ): ?UploadedFile {
        $file = $request->file('image') ?? $request->files->get('image');

        if (
            !$file instanceof UploadedFile ||
            $file->getError() === UPLOAD_ERR_NO_FILE
        ) {
            if ($required) {
                throw ValidationException::withMessages([
                    'image' => 'Please upload an image.',
                ]);
            }

            return null;
        }

        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                'image' => $file->getErrorMessage()
                    ?: 'The image failed to upload. Please try a smaller file.',
            ]);
        }

        $ext = strtolower(
            (string) $file->getClientOriginalExtension()
        );

        if (!in_array($ext, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'image' => 'The image must be a JPG, PNG, WEBP, GIF or SVG file.',
            ]);
        }

        return $file;
    }

    private function uploadImage(UploadedFile $file): string
    {
        $dir = public_path(
            'frontend/assets/images/home-highlights'
        );

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $name = time()
            . '_'
            . uniqid()
            . '_highlight.'
            . $ext;

        $file->move($dir, $name);

        $relative = 'frontend/assets/images/home-highlights/' . $name;

        if (!is_file(public_path($relative))) {
            throw ValidationException::withMessages([
                'image' => 'The image could not be saved. Please try again.',
            ]);
        }

        return $relative;
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

        if (!str_contains($image, 'home-highlights/')) {
            return;
        }

        $path = public_path($image);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}