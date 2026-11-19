<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;

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
        $data['features'] = array_values($features); // Reindex array

        // ✅ FIXED: Handle background image upload with proper path
        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($specialOffer->background_image) {
                $this->deleteBackgroundImage($specialOffer->background_image);
            }

            $image = $request->file('background_image');
            $filename = time() . '_' . uniqid() . '_offer.' . $image->getClientOriginalExtension();
            
            $directory = base_path('backend/assets/images/special-offers');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $image->move($directory, $filename);
            
            // ✅ Store the full relative path (not just filename)
            $data['background_image'] = 'backend/assets/images/special-offers/' . $filename;
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

    private function deleteBackgroundImage(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        // ✅ Clean the path
        $imagePath = trim($imagePath);
        $imagePath = str_replace('\\', '/', $imagePath);
        $imagePath = ltrim($imagePath, '/');

        // If it's a full URL, extract the path
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return;
        }

        // Remove 'public/' if present
        if (str_starts_with($imagePath, 'public/')) {
            $imagePath = substr($imagePath, 7);
        }

        $fullPath = base_path($imagePath);
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}