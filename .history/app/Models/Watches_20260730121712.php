<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watches extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'price',
        'recommended_size',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset($this->image);
        }

        return match ($this->slug) {
            'for-him' => asset('frontend/assets/images/catg_Mens.jpg'),
            'for-her' => asset('frontend/assets/images/catg_Womens.jpg'),
            default => asset('frontend/assets/images/catg_Mens.jpg'),
        };
    }

    public function getListingUrlAttribute(): string
    {
        $gender = match ($this->slug) {
            'for-him' => 'men',
            'for-her' => 'women',
            default => null,
        };

        if ($gender) {
            return route('product.listing', ['gender' => $gender]);
        }

        return route('product.listing');
    }
}
