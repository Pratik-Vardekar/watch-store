<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeServiceItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'image_updated_at',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'image_updated_at' => 'datetime',
    ];

    public function getImageUrlAttribute(): string
    {
        $image = trim((string) $this->image);

        if ($image !== '') {
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $image;
            }

            $path = ltrim(str_replace('\\', '/', $image), '/');

            return $this->withCacheBuster(asset($path));
        }

        return $this->withCacheBuster($this->default_image_url);
    }

    public function getImageChangedAtAttribute()
    {
        return $this->image_updated_at ?? $this->updated_at;
    }

    private function withCacheBuster(string $url): string
    {
        $version = optional($this->image_changed_at)->timestamp ?? time();

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $version;
    }

    public function getDefaultImageUrlAttribute(): string
    {
        $title = strtolower(trim((string) $this->title));

        $map = [
            'battery' => 'frontend/assets/images/serv_Battery.jpg',
            'strap' => 'frontend/assets/images/serv_Strap.jpg',
            'glass' => 'frontend/assets/images/serv_Glass.jpg',
            'mechanical' => 'frontend/assets/images/serv_Mechanical.jpg',
        ];

        return asset($map[$title] ?? 'frontend/assets/images/serv_Battery.jpg');
    }
}
