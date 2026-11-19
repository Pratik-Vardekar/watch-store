<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HowToPlaySection;
use Illuminate\Http\Request;

class HowToPlaySectionController extends Controller
{
    public function edit()
    {
        $section = HowToPlaySection::firstOrCreate([], [
            'title' => "How To Play The\nWord Hunt Challenge!!!",
            'youtube_url' => '',
            'button_text' => 'View All',
            'button_url' => '#',
        ]);

        return view('admin.how-to-play-section.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
        ]);

        $section = HowToPlaySection::firstOrCreate([]);

        $data = $request->only([
            'title',
            'youtube_url',
            'button_text',
            'button_url',
        ]);

        if ($request->hasFile('thumbnail')) {
            $dir = public_path('backend/assets/images/how-to-play');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($section->thumbnail && file_exists(public_path($section->thumbnail))) {
                @unlink(public_path($section->thumbnail));
            }

            $file = $request->file('thumbnail');
            $name = time() . '_howtoplay_thumb.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $data['thumbnail'] = 'backend/assets/images/how-to-play/' . $name;
        }

        if ($request->hasFile('image')) {
            $dir = public_path('backend/assets/images/how-to-play');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($section->image && file_exists(public_path($section->image))) {
                @unlink(public_path($section->image));
            }

            $file = $request->file('image');
            $name = time() . '_howtoplay_robot.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $data['image'] = 'backend/assets/images/how-to-play/' . $name;
        }

        $section->update($data);

        return back()->with('success', 'How To Play section updated successfully.');
    }
}
