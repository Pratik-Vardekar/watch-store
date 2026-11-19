<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Watches;
use Illuminate\Http\Request;

class WatchesController extends Controller
{
    public function index()
    {
        $watches = Watches::orderBy('sort_order')->get();

        return view('admin.watches.index', compact('watches'));
    }

    public function update(Request $request)
    {
        foreach ($request->watch_ids ?? [] as $id) {
            $watch = Watches::find($id);

            if (!$watch) {
                continue;
            }

            if ($request->hasFile("image.$id")) {
                $image = $request->file("image.$id");
                $filename = time() . '_' . $id . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/watches'), $filename);

                $watch->image = 'uploads/watches/' . $filename;
                $watch->save();
            }
        }

        return redirect()->back()
            ->with('success', 'Watches banners updated successfully.');
    }
}
