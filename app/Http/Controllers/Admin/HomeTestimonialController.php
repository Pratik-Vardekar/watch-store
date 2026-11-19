<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeTestimonial;
use Illuminate\Http\Request;

class HomeTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = HomeTestimonial::orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.home-testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.home-testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'review' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        HomeTestimonial::create([
            'name' => $request->name,
            'review' => $request->review,
            'rating' => $request->rating,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.home-testimonials.index')
            ->with('success', 'Testimonial added successfully.');
    }

    public function edit(HomeTestimonial $homeTestimonial)
    {
        return view('admin.home-testimonials.edit', compact('homeTestimonial'));
    }

    public function update(Request $request, HomeTestimonial $homeTestimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'review' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $homeTestimonial->update([
            'name' => $request->name,
            'review' => $request->review,
            'rating' => $request->rating,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.home-testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(HomeTestimonial $homeTestimonial)
    {
        $homeTestimonial->delete();

        return redirect()
            ->route('admin.home-testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
}
