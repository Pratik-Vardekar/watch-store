<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'link',
        'status',
        'position',
    ];

    public function getLogoUrlAttribute()
    {
        return asset('backend/assets/images/brands/' . $this->logo);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
