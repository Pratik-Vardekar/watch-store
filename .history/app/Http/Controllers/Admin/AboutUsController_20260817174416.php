<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUsSection::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'About Us',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'image' => null,
            ]
        );

        return view('admin.about-us.index', compact('about'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $about = AboutUsSection::firstOrCreate(['id' => 1]);

        $data = [
            'title'   => $request->title ?? 'About Us',
            'content' => $request->content ?? '',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($about->image && file_exists(base_path('backend/assets/images/about/' . $about->image))) {
                unlink(base_path('backend/assets/images/about/' . $about->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_about.' . $image->getClientOriginalExtension();
            $image->move(base_path('backend/assets/images/about'), $imageName);
            $data['image'] = $imageName;
        }

        $about->update($data);

        return redirect()
            ->route('about-us.index')
            ->with('success', 'About page updated successfully.');
    }
}