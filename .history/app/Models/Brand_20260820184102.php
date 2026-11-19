<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'description',
        'link',
        'status',
        'position',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return asset('frontend/assets/images/placeholder-brand.png');
        }
        
        $logoPath = base_path('backend/assets/images/brands/' . $this->logo);
        if (file_exists($logoPath)) {
            return asset('backend/assets/images/brands/' . $this->logo);
        }
        
        return asset('frontend/assets/images/placeholder-brand.png');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }
}