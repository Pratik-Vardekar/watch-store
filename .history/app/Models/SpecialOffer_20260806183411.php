<?php
// app/Models/SpecialOffer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

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
            return asset('storage/special-offers/' . $this->background_image);
        }
        return null;
    }

    // Get features as array
    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }
}