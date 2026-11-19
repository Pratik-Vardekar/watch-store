<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeFaq;
use Illuminate\Http\Request;

class HomeFaqController extends Controller
{
    public function index()
    {
        $faqs = HomeFaq::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.home-faqs.index', compact('faqs'));
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
}
