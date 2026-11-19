<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
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
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

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

    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }
}
