<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
        'image', // Add this if you have a direct image column
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the images for the service
     */
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the first image URL
     */
    public function getFirstImageUrlAttribute(): string
    {
        $image = $this->images->first();

        if ($image && $image->image) {
            return asset('backend/assets/images/services/' . $image->image);
        }

        return asset('frontend/assets/images/no-image.png');
    }

// app/Models/Service.php

public function getImageUrlAttribute()
{
    if (!$this->image) {
        return asset('frontend/assets/images/no-image.png');
    }

    // If image already has the full path
    if (strpos($this->image, 'frontend/assets/images/home-service-items/') === 0) {
        return asset($this->image);
    }

    // If image path starts with 'uploads/'
    if (strpos($this->image, 'uploads/') === 0) {
        return asset($this->image);
    }

    // If image is just a filename
    return asset('frontend/assets/images/home-service-items/' . $this->image);
}

public function getDefaultImageUrlAttribute()
{
    return asset('frontend/assets/images/no-image.png');
}
}