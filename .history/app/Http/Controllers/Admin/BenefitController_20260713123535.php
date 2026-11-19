<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use Illuminate\Http\Request;

class BenefitController extends Controller
{

    public function index()
    {
        $benefits = Benefit::latest()->get();
        return view('admin.benefits.index', compact('benefits'));
    }

    public function edit($id)
    {
        $benefit = Benefit::findOrFail($id);
        return view('admin.benefits.edit', compact('benefit'));
    }

    public function update(Request $request, $id)
    {
        $benefit = Benefit::findOrFail($id);

        // Only update SVG logo field, not file/image upload
        $benefit->update([
            'title' => $request->title,
            'description' => $request->description,
            'svg_logo' => $request->svg_logo,
            'status' => $request->status
        ]);

        return redirect()
            ->route('benefits.index')
            ->with('success', 'Benefit Updated');
    }
}
