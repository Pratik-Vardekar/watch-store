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

    // app/Models/Service.php

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('frontend/assets/images/no-image.png');
        }

        // If image starts with http or https, it's a full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Check if image path already has 'uploads/services/'
        if (strpos($this->image, 'uploads/services/') === 0) {
            return asset($this->image);
        }

        // Default: image is just a filename in uploads/services/
        return asset('uploads/services/' . $this->image);sec=
    }
}
