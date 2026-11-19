<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
        'logo_url',
        'features_list',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_link',
        'badge_text',
        'background_image',
        'logo',
        'bg_color',
        'text_color',
        'features',
        'feature_icons', // Add this
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'feature_icons' => 'array', // Add this
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function getActiveOffer()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->background_image) {
            return null;
        }

        $stored = ltrim(str_replace('\\', '/', trim($this->background_image)), '/');

        if (str_starts_with($stored, 'http://') || str_starts_with($stored, 'https://')) {
            return $stored;
        }

        $candidates = [$stored];

        if (! str_contains($stored, '/')) {
            $candidates[] = 'uploads/special-offers/' . $stored;
            $candidates[] = 'backend/assets/images/special-offers/' . $stored;
            $candidates[] = 'storage/special-offers/' . $stored;
        }

        foreach (array_unique($candidates) as $path) {
            if (is_file(public_path($path)) || is_file(base_path($path))) {
                return asset($path);
            }
        }

        return asset($candidates[0]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        $stored = ltrim(str_replace('\\', '/', trim($this->logo)), '/');

        if (str_starts_with($stored, 'http://') || str_starts_with($stored, 'https://')) {
            return $stored;
        }

        $candidates = [$stored];

        if (! str_contains($stored, '/')) {
            $candidates[] = 'uploads/special-offers/logos/' . $stored;
            $candidates[] = 'backend/assets/images/special-offers/logos/' . $stored;
            $candidates[] = 'storage/special-offers/logos/' . $stored;
        }

        foreach (array_unique($candidates) as $path) {
            if (is_file(public_path($path)) || is_file(base_path($path))) {
                return asset($path);
            }
        }

        return asset($candidates[0]);
    }

    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }

    // Get feature icon URL for a specific feature index
    public function getFeatureIconUrl($index): ?string
    {
        $icons = $this->feature_icons ?? [];
        
        if (!isset($icons[$index])) {
            return null;
        }

        $icon = $icons[$index];
        
        // If it's a URL or SVG, return as is
        if (str_starts_with($icon, 'http') || str_starts_with($icon, '<svg')) {
            return $icon;
        }

        // If it's a file path
        $stored = ltrim(str_replace('\\', '/', trim($icon)), '/');

        if (is_file(public_path($stored)) || is_file(base_path($stored))) {
            return asset($stored);
        }

        return asset($stored);
    }
}