<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(10);
        return view('admin.gallery.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp',
            'status' => 'nullable|boolean',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(
                public_path('backend/assets/images/gallery'),
                $imageName
            );
        }

        Gallery::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'image' => $imageName,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('gallery.index')->with('success', 'Gallery Added Successfully');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status' => 'nullable|boolean',
        ]);

        $imageName = $gallery->image;

        if ($request->hasFile('image')) {

            $oldPath = public_path('backend/assets/images/gallery/' . $gallery->image);

            if ($gallery->image && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(
                public_path('backend/assets/images/gallery'),
                $imageName
            );
        }

        $gallery->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'image' => $imageName,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('gallery.index')->with('success', 'Gallery Updated Successfully');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image) {
            $path = public_path('backend/assets/images/gallery/' . $gallery->image);

            if (file_exists($path)) {
                unlink($path);
            }
        }

        $gallery->delete();

        return redirect()->back()->with('success', 'Gallery Deleted Successfully');
    }

    // Show images list
    public function images(Gallery $gallery)
    {
        $images = $gallery->images()->latest()->paginate(12);
        return view('admin.gallery.images.index', compact('gallery', 'images'));
    }

    // Show create image form
    public function createImage(Gallery $gallery)
    {
        return view('admin.gallery.images.create', compact('gallery'));
    }

    // Store multiple images
    public function storeImage(Request $request, Gallery $gallery)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
            // 'title' => 'nullable|string|max:255', // Title for individual image if you want per-image titles; may need to adapt your UI for this.
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                $file->move(
                    public_path('backend/assets/images/gallery'),
                    $imageName
                );

                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image'      => $imageName,
                    // 'title'      => null, // No title here; modify if you want per-image titles.
                ]);
            }
        }

        return redirect()
            ->route('gallery.images.index', $gallery->id)
            ->with('success', 'Images uploaded successfully');
    }

    // Delete image
    public function destroyImage(Gallery $gallery, GalleryImage $image)
    {
        if ($image->image) {

            $path = public_path('backend/assets/images/gallery/' . $image->image);

            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }
        }

        $image->delete();

        return back()->with('success', 'Image deleted');
    }
}