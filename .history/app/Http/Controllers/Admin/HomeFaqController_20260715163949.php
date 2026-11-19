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
        $faqs = HomeFaq::orderBy('sort_order')->orderBy('id')->get();
        $section = HomeFaqSection::firstOrCreate([]);

        return view('admin.home-faqs.index', compact('faqs', 'section'));
    }

    public function create()
    {
        return view('admin.home-faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        HomeFaq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ added successfully.');
    }

    public function edit(HomeFaq $homeFaq)
    {
        return view('admin.home-faqs.edit', compact('homeFaq'));
    }

    public function update(Request $request, HomeFaq $homeFaq)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $homeFaq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(HomeFaq $homeFaq)
    {
        $homeFaq->delete();

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    /**
     * Update FAQ section side image (PNG / image).
     */
    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $section = HomeFaqSection::firstOrCreate([]);
        $dir = public_path('backend/assets/images/home-faqs');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($section->image && file_exists(public_path($section->image))) {
            @unlink(public_path($section->image));
        }

        $file = $request->file('image');
        $name = time() . '_faq_kid.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        $section->update([
            'image' => 'backend/assets/images/home-faqs/' . $name,
        ]);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ section image updated successfully.');
    }
}
