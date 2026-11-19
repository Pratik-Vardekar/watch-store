<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{

    public function index()
    {
        $features = Feature::latest()->get();
        return view('admin.features.index', compact('features'));
    }

    public function edit($id)
    {
        $feature = Feature::findOrFail($id);
        return view('admin.features.edit', compact('feature'));
    }

    public function update(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);

        $image = $feature->image;

        if ($request->hasFile('image')) {

            $image = time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/features'),
                $image
            );
        }

        $feature->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'Feature Updated');
    }
}
