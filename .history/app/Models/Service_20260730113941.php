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
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getFirstImageUrlAttribute(): string
    {
        $image = $this->images->first();

        if ($image && $image->image) {
            return asset('backend/assets/images/services/' . $image->image);
        }

        return asset('frontend/assets/images/no-image.png');
    }
}
