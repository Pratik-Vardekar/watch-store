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
    public function getLogoUrlAttribute($value)
    {
        if ($value) {
            return asset($value);
        }
        
        if ($this->logo) {
            return asset('backend/assets/images/brand/' . $this->logo);
        }
        
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