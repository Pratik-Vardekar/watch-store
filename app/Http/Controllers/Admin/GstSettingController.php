<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GSTSetting;

class GstSettingController extends Controller
{
    public function index()
{
    $gstSettings = GSTSetting::latest()->paginate(10);

    return view('admin.gst_settings.index', compact('gstSettings'));
}

public function create()
{
    return view('admin.gst_settings.create');
}
public function store(Request $request)
{
    $request->validate([
        'gst_percentage' => 'required|numeric|min:0|max:100',
    ]);

    GSTSetting::create([
        'gst_percentage' => $request->gst_percentage,
        'status' => $request->status ? 1 : 0,
    ]);

    return redirect()
    ->route('gst.settings.index')
    ->with('success', 'GST slab added successfully.');}
public function edit(GSTSetting $gstSetting)
{
    return view('admin.gst_settings.edit', compact('gstSetting'));
}
public function update(Request $request, GSTSetting $gstSetting)
{
    $request->validate([
        'gst_percentage' => 'required|numeric|min:0|max:100',
    ]);

    $gstSetting->update([
        'gst_percentage' => $request->gst_percentage,
        'status' => $request->status ? 1 : 0,
    ]);

    return redirect()
    ->route('gst.settings.index')
    ->with('success', 'GST slab updated successfully.');}
public function destroy(GSTSetting $gstSetting)
{
    $gstSetting->delete();

    return redirect()
    ->route('gst.settings.index')
    ->with('success', 'GST slab deleted successfully.');
}
public function status($id)
{
    $gst = GSTSetting::findOrFail($id);

    $gst->update([
        'status' => !$gst->status
    ]);

    return back()->with('success', 'GST status updated successfully.');
}
}