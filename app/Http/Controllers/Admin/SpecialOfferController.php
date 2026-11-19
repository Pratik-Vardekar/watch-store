<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use RuntimeException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialOfferController extends Controller
{
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
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'bg_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string|max:255',
            // Feature icons validation - can be file or text
            'feature_icons' => 'nullable|array',
            'feature_icons.*' => 'nullable|string|max:5000',
            'feature_icon_files' => 'nullable|array',
            'feature_icon_files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
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
        
        // Handle feature icons with file uploads
        $data['feature_icons'] = $this->processFeatureIcons(
            $request->input('feature_icons', []),
            $request->file('feature_icon_files', []),
            $specialOffer
        );

        // Handle background image
        if ($request->hasFile('background_image')) {
            $this->deleteFile($specialOffer->background_image);
            $data['background_image'] = $this->uploadFile($request->file('background_image'), 'special-offers');
        }

        // Handle main logo
        if ($request->hasFile('logo')) {
            $this->deleteFile($specialOffer->logo);
            $data['logo'] = $this->uploadFile($request->file('logo'), 'special-offers/logos');
        }

        $specialOffer->update($data);

        return redirect()->route('admin.special-offers.edit', $specialOffer)
            ->with('success', 'Special offer updated successfully.');
    }

    public function toggleStatus(SpecialOffer $specialOffer)
    {
        $specialOffer->update(['is_active' => ! $specialOffer->is_active]);
        $status = $specialOffer->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.special-offers.index')
            ->with('success', "Special offer has been {$status} successfully.");
    }

    private function uploadFile($file, string $subDirectory): string
    {
        if (! $file || ! $file->isValid()) {
            throw new RuntimeException('File upload failed. Please try again.');
        }

        $directory = base_path('uploads/' . $subDirectory);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create uploads/' . $subDirectory . ' folder.');
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $filename = time() . '_' . uniqid() . '_' . Str::random(8) . '.' . $extension;
        $file->move($directory, $filename);

        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($fullPath)) {
            throw new RuntimeException('File upload failed. Please try again.');
        }

        @chmod($fullPath, 0644);

        return 'uploads/' . $subDirectory . '/' . $filename;
    }

    private function processFeatureIcons(array $iconTexts, array $iconFiles, SpecialOffer $specialOffer): array
    {
        $existingIcons = $specialOffer->feature_icons ?? [];
        $processedIcons = [];

        // Get max count between text inputs and files
        $maxCount = max(count($iconTexts), count($iconFiles));

        for ($i = 0; $i < $maxCount; $i++) {
            $iconValue = '';

            // Check if there's a file upload for this index
            if (isset($iconFiles[$i]) && $iconFiles[$i] instanceof \Illuminate\Http\UploadedFile) {
                // Delete existing file for this icon if exists
                if (isset($existingIcons[$i]) && !filter_var($existingIcons[$i], FILTER_VALIDATE_URL) && !Str::startsWith($existingIcons[$i], '<')) {
                    $this->deleteFile($existingIcons[$i]);
                }
                $iconValue = $this->uploadFile($iconFiles[$i], 'special-offers/feature-icons');
            } 
            // Check if there's text input (SVG or URL)
            elseif (isset($iconTexts[$i]) && !empty(trim($iconTexts[$i]))) {
                $iconValue = trim($iconTexts[$i]);
            }
            // Keep existing if available
            elseif (isset($existingIcons[$i])) {
                $iconValue = $existingIcons[$i];
            }

            $processedIcons[] = $iconValue;
        }

        return $processedIcons;
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
                    'title' => trim((string) ($feature['title'] ?? '')),
                    'description' => trim((string) ($feature['description'] ?? '')),
                ];

                if ($normalized['title'] === '' && $normalized['description'] === '') {
                    return null;
                }

                return array_filter($normalized, fn ($value) => $value !== '');
            })
            ->filter()
            ->values()
            ->all();
    }

    private function deleteFile(?string $filePath): void
    {
        if (! $filePath) {
            return;
        }

        $filePath = ltrim(str_replace('\\', '/', trim($filePath)), '/');

        // Don't delete URLs or SVG code
        if (str_starts_with($filePath, 'http://') || 
            str_starts_with($filePath, 'https://') || 
            str_starts_with($filePath, '<')) {
            return;
        }

        if (str_starts_with($filePath, 'public/')) {
            $filePath = substr($filePath, 7);
        }

        $fullPath = base_path($filePath);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}