<?php
// app/Http/Controllers/Admin/SpecialOfferController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecialOfferController extends Controller
{
    public function index()
    {
        $offers = SpecialOffer::orderBy('sort_order')->get();
        return view('admin.special-offers.index', compact('offers'));
    }

    public function edit(SpecialOffer $specialOffer)
    {
        return view('admin.special-offers.edit', compact('specialOffer'));
    }

    public function update(Request $request, SpecialOffer $specialOffer)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'required|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bg_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $data = $request->only([
            'title',
            'subtitle',
            'description',
            'button_text',
            'button_link',
            'badge_text',
            'bg_color',
            'text_color',
            'is_active'
        ]);

        // Handle features
        $features = $request->input('features', []);
        $features = array_filter($features, function($value) {
            return !empty(trim($value));
        });
        $data['features'] = $features;

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($specialOffer->background_image) {
                $oldPath = 'special-offers/' . $specialOffer->background_image;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $image = $request->file('background_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('special-offers', $filename, 'public');
            $data['background_image'] = $filename;
        }

        $specialOffer->update($data);

        return redirect()->route('admin.special-offers.index')
            ->with('success', 'Special offer updated successfully.');
    }

    public function toggleStatus(SpecialOffer $specialOffer)
    {
        $specialOffer->update(['is_active' => !$specialOffer->is_active]);
        $status = $specialOffer->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.special-offers.index')
            ->with('success', "Special offer has been {$status} successfully.");
    }
}