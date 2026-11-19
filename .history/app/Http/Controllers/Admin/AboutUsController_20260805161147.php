<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsSection;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUsSection::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'About Us',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ]
        );

        return view('admin.about-us.index', compact('about'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        $about = AboutUsSection::firstOrCreate(['id' => 1]);

        $about->update([
            'title'   => $request->title ?? 'About Us',
            'content' => $request->content ?? '',
        ]);

        return redirect()
            ->route('about-us.index')
            ->with('success', 'About page updated successfully.');
    }
}