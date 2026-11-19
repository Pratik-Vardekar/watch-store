<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUsSection extends Model
{
    protected $table = 'about_us_sections';

    protected $fillable = [
        'title',
        'content',
        'image',
        'created_at',
        'updated_at',
    ];

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('backend/assets/images/about/' . $this->image);
        }
        return asset('frontend/assets/images/heroBanner_2.jpg');
    }
}