<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
     public function index(Request $request)
    {
        $query = Slider::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $sliders = $query->latest()->paginate(10);

        return view('admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.slider.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required',
            'subtitle'  => 'nullable|string|max:255',
            'link'      => 'nullable|url',
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp',
            'status'    => 'required',
        ]);
        

        $imageName = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imageName = time() . rand(111,999) . '.' . $image->extension();

            $image->move(
                public_path('backend/assets/images/slider'),
                $imageName
            );
        }

        Slider::create([
            'title'       => $request->title,
            'description' => $request->description,
            'link'        => $request->link,
            'image'       => $imageName,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Added Successfully');
    }

    public function edit(Slider $slider)
    {
        return view('admin.slider.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title'  => 'required',
            'link'   => 'nullable|url',
            'status' => 'required',
        ]);

        $imageName = $slider->image;

        if ($request->hasFile('image')) {

            if ($slider->image) {

                $oldPath = public_path('backend/assets/images/slider/' . $slider->image);

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image = $request->file('image');

            $imageName = time() . rand(111,999) . '.' . $image->extension();

            $image->move(
                public_path('backend/assets/images/slider'),
                $imageName
            );
        }

        $slider->update([
            'title'       => $request->title,
            'description' => $request->description,
            'link'        => $request->link,
            'image'       => $imageName,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Updated Successfully');
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image) {

            $path = public_path('backend/assets/images/slider/' . $slider->image);

            if (file_exists($path)) {
                unlink($path);
            }
        }

        $slider->delete();

        return redirect()
            ->route('slider.index')
            ->with('success', 'Slider Deleted Successfully');
    }

    public function toggleStatus(Slider $slider)
    {
        $slider->status = $slider->status == 1 ? 0 : 1;

        $slider->save();

        return back()->with('success', 'Slider status updated successfully');
    }

    public function export(Request $request)
    {
        $query = Slider::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('subtitle', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $sliders = $query->latest()->get();

        $fileName = 'sliders.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($sliders) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID',
                'Title',
                'Subtitle',
                'Image',
                'Status',
                'Created At'
            ]);

            foreach ($sliders as $slider) {
                fputcsv($file, [
                    $slider->id,
                    $slider->title ?? '-',
                    $slider->subtitle ?? '-',
                    $slider->image,
                    $slider->status == 1 ? 'Active' : 'Inactive',
                    $slider->created_at->format('d-m-Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}