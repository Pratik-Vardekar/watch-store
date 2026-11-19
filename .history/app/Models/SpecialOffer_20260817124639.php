<?php
// app/Models/SpecialOffer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'bg_color',
        'text_color',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Get single active offer
    public static function getActiveOffer()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    // Get featured image URL
    public function getImageUrlAttribute()
    {
        if ($this->background_image) {
            $filename = basename($this->background_image);
            $publicPath = public_path('backend/assets/images/special-offers/' . $filename);

            if (file_exists($publicPath)) {
                return asset('backend/assets/images/special-offers/' . $filename);
            }

            $storagePath = 'special-offers/' . $filename;
            if (Storage::disk('public')->exists($storagePath)) {
                return asset('storage/' . $storagePath);
            }
        }
        return null;
    }

    // Get features as array
    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }
}