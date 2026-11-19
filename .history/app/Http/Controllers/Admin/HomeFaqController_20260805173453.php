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
        
        // Fix: Provide title when creating
        $section = HomeFaqSection::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Frequently Asked Questions',
                'subtitle' => 'Find answers to common questions about our watches and services.',
                'status' => 1,
                'sort_order' => 0,
            ]
        );

        return view('admin.home-faqs.index', compact('faqs', 'section'));
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
            'status' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $homeFaq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        HomeFaq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status ?? 1,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ added successfully.');
    }

    public function destroy(HomeFaq $homeFaq)
    {
        $homeFaq->delete();

        return redirect()
            ->route('admin.home-faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }
}