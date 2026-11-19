<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use RuntimeException;

class SpecialOfferController extends Controller
{
    // public function index()
    // {
    //     $offers = SpecialOffer::orderBy('sort_order')->get();

    //     return view('admin.special-offers.index', compact('offers'));
    // }

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
            'background_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048', // Add logo validation
            'bg_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string|max:5000',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string|max:255',
            'is_active' => 'nullable|in:0,1',
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
        ]);
    
        $data['sort_order'] = $request->input('sort_order', 0);
        $data['is_active'] = $request->input('is_active', '0') === '1';
        $data['features'] = $this->normalizeFeatures($request->input('features', []));
    
        // Handle background image
        if ($request->hasFile('background_image')) {
            $this->deleteBackgroundImage($specialOffer->background_image);
            $data['background_image'] = $this->uploadBackgroundImage($request->file('background_image'));
        }
    
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $this->deleteBackgroundImage($specialOffer->logo); // Reuse delete method for logo
            $data['logo'] = $this->uploadLogoImage($request->file('logo'));
        }
    
        $specialOffer->update($data);
    
        return redirect()->route('admin.special-offers.edit', $specialOffer)
            ->with('success', 'Special offer updated successfully.');
    }

    private function uploadLogoImage($file): string
    {
        if (! $file || ! $file->isValid()) {
            throw new RuntimeException('Logo upload failed. Please try again.');
        }

        $directory = base_path('uploads/special-offers/logos');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create uploads/special-offers/logos folder.');
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'png'
        );

        $filename = 'logo_' . time() . '_' . uniqid() . '.' . $extension;
        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($fullPath)) {
            throw new RuntimeException('Logo upload failed. Please try again.');
        }

        @chmod($fullPath, 0644);

        return 'uploads/special-offers/logos/' . $filename;
    }

    public function toggleStatus(SpecialOffer $specialOffer)
    {
        $specialOffer->update(['is_active' => ! $specialOffer->is_active]);
        $status = $specialOffer->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.special-offers.index')
            ->with('success', "Special offer has been {$status} successfully.");
    }

    private function uploadBackgroundImage($file): string
    {
        if (! $file || ! $file->isValid()) {
            throw new RuntimeException('Image upload failed. Please try again.');
        }

        $directory = base_path('uploads/special-offers');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create uploads/special-offers folder.');
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $filename = time() . '_' . uniqid() . '_offer.' . $extension;
        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($fullPath)) {
            throw new RuntimeException('Image upload failed. Please try again.');
        }

        @chmod($fullPath, 0644);

        return 'uploads/special-offers/' . $filename;
    }

    private function normalizeFeatures(array $features): array
    {
        return collect($features)
            ->map(function ($feature) {
                if (is_string($feature)) {
                    $feature = trim($feature);

                    return $feature !== '' ? ['title' => $feature] : null;
                }

                if (! is_array($feature)) {
                    return null;
                }

                $normalized = [
                    'icon' => trim((string) ($feature['icon'] ?? '')),
                    'title' => trim((string) ($feature['title'] ?? '')),
                    'description' => trim((string) ($feature['description'] ?? '')),
                ];

                if ($normalized['icon'] === '' && $normalized['title'] === '' && $normalized['description'] === '') {
                    return null;
                }

                return array_filter($normalized, fn ($value) => $value !== '');
            })
            ->filter()
            ->values()
            ->all();
    }

    private function deleteBackgroundImage(?string $imagePath): void
    {
        if (! $imagePath) {
            return;
        }

        $imagePath = ltrim(str_replace('\\', '/', trim($imagePath)), '/');

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return;
        }

        if (str_starts_with($imagePath, 'public/')) {
            $imagePath = substr($imagePath, 7);
        }

        $candidates = [$imagePath];

        if (! str_contains($imagePath, '/')) {
            $candidates[] = 'uploads/special-offers/' . $imagePath;
            $candidates[] = 'backend/assets/images/special-offers/' . $imagePath;
            $candidates[] = 'storage/special-offers/' . $imagePath;
        }

        foreach (array_unique($candidates) as $relativePath) {
            foreach ([base_path($relativePath), base_path($relativePath)] as $fullPath) {
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
    }
}
