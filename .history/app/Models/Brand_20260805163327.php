<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'logo_url',
        'description',
        'status',
        'position',
        'link',
    ];

    // Accessor for logo URL
    public function getLogoUrlAttribute()
    {
        // If logo_url is set and not empty, use it
        if (!empty($this->attributes['logo_url'])) {
            return asset($this->attributes['logo_url']);
        }
        
        // If logo is set and not empty, use it
        if (!empty($this->attributes['logo'])) {
            // Check if logo is stored in different directories
            $logoPath = $this->attributes['logo'];
            
            // If path already contains 'backend/assets/images/brand/', use as is
            if (strpos($logoPath, 'backend/assets/images/brand/') !== false) {
                return asset($logoPath);
            }
            
            // Default path
            return asset('backend/assets/images/brand/' . $logoPath);
        }
        
        // Fallback default image
        return asset('frontend/assets/images/brands/default.jpg');
    }

    // Boot method for auto-generating slug
    public static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}