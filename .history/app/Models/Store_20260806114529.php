<?php
// app/Models/Store.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'timing',
        'google_maps_link',
        'latitude',
        'longitude',
        'images',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->name);
            }
        });

        static::updating(function ($store) {
            if ($store->isDirty('name')) {
                $store->slug = Str::slug($store->name);
            }
        });
    }

    // Accessor for primary image
    public function getPrimaryImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return $this->images[0];
        }
        return null;
    }

    // Get all images
    public function getAllImagesAttribute()
    {
        return $this->images ?? [];
    }
}