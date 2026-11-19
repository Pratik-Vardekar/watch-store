<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'link',
        'image',
        'mobile_image',
        'sort_order',
        'status',
    ];

    /**
     * Clean null strings and empty values for title
     */
    public function getTitleAttribute($value)
    {
        return ($value === 'null' || $value === '' || $value === null) ? null : $value;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ($value === 'null' || $value === null) ? '' : trim($value);
    }

    /**
     * Clean null strings and empty values for subtitle
     */
    public function getSubtitleAttribute($value)
    {
        return ($value === 'null' || $value === '' || $value === null) ? null : $value;
    }

    public function setSubtitleAttribute($value)
    {
        $this->attributes['subtitle'] = ($value === 'null' || $value === null || trim((string)$value) === '') ? null : trim((string)$value);
    }

    /**
     * Clean null strings and empty values for description
     */
    public function getDescriptionAttribute($value)
    {
        return ($value === 'null' || $value === '' || $value === null) ? null : $value;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = ($value === 'null' || $value === null || trim((string)$value) === '') ? null : trim((string)$value);
    }

    /**
     * Clean null strings and empty values for link
     */
    public function getLinkAttribute($value)
    {
        return ($value === 'null' || $value === '' || $value === null) ? null : $value;
    }

    public function setLinkAttribute($value)
    {
        $this->attributes['link'] = ($value === 'null' || $value === null || trim((string)$value) === '') ? null : trim((string)$value);
    }

    /**
     * Robust resolution of slider image URL across all directory structures
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image) || $this->image === 'null') {
            return asset('frontend/assets/images/heroBanner_1.jpg');
        }

        $image = trim((string)$this->image);

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $cleanPath = ltrim(str_replace('\\', '/', $image), '/');

        // Check potential candidate relative paths
        $candidates = [];

        if (str_starts_with($cleanPath, 'backend/') || str_starts_with($cleanPath, 'frontend/') || str_starts_with($cleanPath, 'uploads/')) {
            $candidates[] = $cleanPath;
        } else {
            $candidates[] = 'backend/assets/images/slider/' . $cleanPath;
            $candidates[] = 'uploads/slider/' . $cleanPath;
            $candidates[] = 'frontend/assets/images/slider/' . $cleanPath;
            $candidates[] = 'frontend/assets/images/' . $cleanPath;
        }

        foreach ($candidates as $candidate) {
            $publicFile = public_path($candidate);
            $baseFile = base_path($candidate);

            if (file_exists($publicFile) && !is_dir($publicFile)) {
                return asset($candidate);
            }

            if (file_exists($baseFile) && !is_dir($baseFile)) {
                return asset($candidate);
            }
        }

        // Default path fallback
        if (str_starts_with($cleanPath, 'backend/') || str_starts_with($cleanPath, 'frontend/') || str_starts_with($cleanPath, 'uploads/')) {
            return asset($cleanPath);
        }

        return asset('backend/assets/images/slider/' . $cleanPath);
    }

    /**
     * Robust resolution of slider mobile image URL across all directory structures
     */
    public function getMobileImageUrlAttribute(): ?string
    {
        if (empty($this->mobile_image) || $this->mobile_image === 'null') {
            return null;
        }

        $image = trim((string)$this->mobile_image);

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $cleanPath = ltrim(str_replace('\\', '/', $image), '/');

        // Check potential candidate relative paths
        $candidates = [];

        if (str_starts_with($cleanPath, 'backend/') || str_starts_with($cleanPath, 'frontend/') || str_starts_with($cleanPath, 'uploads/')) {
            $candidates[] = $cleanPath;
        } else {
            $candidates[] = 'backend/assets/images/slider/' . $cleanPath;
            $candidates[] = 'uploads/slider/' . $cleanPath;
            $candidates[] = 'frontend/assets/images/slider/' . $cleanPath;
            $candidates[] = 'frontend/assets/images/' . $cleanPath;
        }

        foreach ($candidates as $candidate) {
            $publicFile = public_path($candidate);
            $baseFile = base_path($candidate);

            if (file_exists($publicFile) && !is_dir($publicFile)) {
                return asset($candidate);
            }

            if (file_exists($baseFile) && !is_dir($baseFile)) {
                return asset($candidate);
            }
        }

        // Default path fallback
        if (str_starts_with($cleanPath, 'backend/') || str_starts_with($cleanPath, 'frontend/') || str_starts_with($cleanPath, 'uploads/')) {
            return asset($cleanPath);
        }

        return asset('backend/assets/images/slider/' . $cleanPath);
    }

    /**
     * Returns mobile image url if present, otherwise falls back to smart mobile default or desktop image
     */
    public function getEffectiveMobileImageUrlAttribute(): string
    {
        // 1. If custom mobile image is uploaded and set, use it
        $mobileUrl = $this->mobile_image_url;
        if (!empty($mobileUrl)) {
            return $mobileUrl;
        }

        // 2. Intelligent matching based on desktop image
        $desktopImg = (string)($this->image ?? '');
        $desktopClean = str_replace('\\', '/', $desktopImg);

        if (stripos($desktopClean, 'heroBanner_1') !== false || empty($desktopImg) || $desktopImg === 'null') {
            return asset('frontend/assets/images/heroBanner_mob1.jpg');
        }

        if (stripos($desktopClean, 'heroBanner_2') !== false) {
            return asset('frontend/assets/images/heroBanner_mob2.jpg');
        }

        // 3. If there is a _mob version for the image (e.g. name.jpg -> name_mob.jpg)
        $info = pathinfo($desktopClean);
        if (!empty($info['filename']) && !empty($info['extension'])) {
            $mobCandidate = ($info['dirname'] && $info['dirname'] !== '.' ? $info['dirname'] . '/' : '') . $info['filename'] . '_mob.' . $info['extension'];
            if (file_exists(public_path($mobCandidate)) || file_exists(base_path($mobCandidate))) {
                return asset($mobCandidate);
            }
        }

        // 4. Default fallback: desktop image if available, else default mobile hero banner
        return $this->image_url ?: asset('frontend/assets/images/heroBanner_mob1.jpg');
    }
}
