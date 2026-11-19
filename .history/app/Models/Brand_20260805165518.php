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
        if (!empty($this->attributes['logo_url'])) {
            return asset($this->attributes['logo_url']);
        }
        
        if (!empty($this->attributes['logo'])) {
            $logo = $this->attributes['logo'];
            if (strpos($logo, 'backend/assets/images/') !== false) {
                return asset($logo);
            }
            return asset('backend/assets/images/brand/' . $logo);
        }
        
        return asset('frontend/assets/images/brands/default.jpg');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}