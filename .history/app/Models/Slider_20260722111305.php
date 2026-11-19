<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'description',
        'link',
        'image',
        'status'
    ];

    public function getImageUrlAttribute()
    {
        return asset('backend/assets/images/slider/' . $this->image);
    }
}
