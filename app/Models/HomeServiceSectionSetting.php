<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeServiceSectionSetting extends Model
{
    protected $table = 'home_service_section_settings';

    protected $fillable = [
        'background_image',
        'watch_image_1',
        'watch_image_2',
    ];

    public function getBackgroundImageUrlAttribute(): string
    {
        return $this->imageUrl($this->background_image, 'frontend/assets/images/textureBg.jpg');
    }

    public function getWatchImage1UrlAttribute(): string
    {
        return $this->imageUrl($this->watch_image_1, 'frontend/assets/images/servWatch_1.png');
    }

    public function getWatchImage2UrlAttribute(): string
    {
        return $this->imageUrl($this->watch_image_2, 'frontend/assets/images/servWatch_2.png');
    }

    private function imageUrl(?string $path, string $default): string
    {
        $path = trim((string) $path);

        if ($path !== '') {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return asset(ltrim(str_replace('\\', '/', $path), '/'));
        }

        return asset($default);
    }
}
