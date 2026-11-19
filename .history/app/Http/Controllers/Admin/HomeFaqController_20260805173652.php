<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeFaq;
use App\Models\HomeFaqSection;
use Illuminate\Http\Request;

class HomeFaqController extends Controller
{
    public function index()
    {
        $faqs = HomeFaq::ordered()->get();
        
        // Get or create section with proper data
        $section = HomeFaqSection::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Frequently Asked Questions',
                'description' => 'Find answers to common questions.',
                'status' => 1,
            ]
        );

        return view('admin.home-faqs.index', compact('faqs', 'section'));
    }

    public function create()
    {
        return view('admin.home-faqs.create');
    }

    public function edit(HomeFaq $homeFaq)
    {
        return view('admin.home-faqs.edit', compact('homeFaq'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_faq.' . $image->getClientOriginalExtension();
            $image->move(public_path('backend/assets/images/faq'), $imageName);
            $data['image'] = 'backend/assets/images/faq/' . $imageName;
        }

        HomeFaq::create($data);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ added successfully.');
    }

    public function update(Request $request, HomeFaq $homeFaq)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($homeFaq->image && file_exists(public_path($homeFaq->image))) {
                unlink(public_path($homeFaq->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_faq.' . $image->getClientOriginalExtension();
            $image->move(public_path('backend/assets/images/faq'), $imageName);
            $data['image'] = 'backend/assets/images/faq/' . $imageName;
        }

        $homeFaq->update($data);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(HomeFaq $homeFaq)
    {
        // Delete image if exists
        if ($homeFaq->image && file_exists(public_path($homeFaq->image))) {
            unlink(public_path($homeFaq->image));
        }

        $homeFaq->delete();

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function toggleStatus(HomeFaq $homeFaq)
    {
        $homeFaq->status = $homeFaq->status == 1 ? 0 : 1;
        $homeFaq->save();

        return redirect()
            ->back()
            ->with('success', 'FAQ status updated successfully.');
    }
}