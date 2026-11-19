<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class HomeHighlight extends Model
{
 

    protected $fillable = [
        'title',
        'image',
        'icon',
        'image_updated_at',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'image_updated_at' => 'datetime',
    ];

    public static function ensureDefaults(): void
    {
        if (! Schema::hasTable('home_highlights')) {
            return;
        }

       

        

    public static function imageColumn(): string
    {
        if (Schema::hasColumn('home_highlights', 'image')) {
            return 'image';
        }

        return 'icon';
    }

    public function storedImagePath(): string
    {
        $image = trim((string) ($this->attributes['image'] ?? ''));

        if ($image === '') {
            $image = trim((string) ($this->attributes['icon'] ?? ''));
        }

        return $image;
    }

    public function getImageUrlAttribute(): string
    {
        $image = $this->storedImagePath();

        // Custom logic: If "Perfumes, Pens, Wallets & Alarm Clocks" (sort_order 4), do NOT show default image.
        // Only return image URL if custom image exists, otherwise return empty string for 4th card.

        // Find out if this is the 4th default card by title.
        $doNotShowDefault = false;
        foreach (self::DEFAULT_CARDS as $card) {
            if (
                $card['sort_order'] === 4 &&
                $card['title'] === $this->title
            ) {
                $doNotShowDefault = true;
                break;
            }
        }

        if ($image !== '') {
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                return $this->withCacheBuster($image);
            }

            $path = ltrim(str_replace('\\', '/', $image), '/');
            return $this->withCacheBuster(asset($path));
        }

        // If it's the 4th card, don't return default image, return empty string
        if ($doNotShowDefault) {
            return '';
        }

        return $this->withCacheBuster($this->default_image_url);
    }

    public function getImageChangedAtAttribute()
    {
        return $this->image_updated_at ?? $this->updated_at;
    }

    public function getDefaultImageUrlAttribute(): string
    {
        foreach (self::DEFAULT_CARDS as $card) {
            if ($card['title'] === $this->title) {
                // Don't return a default image if this is the 4th card
                if ($card['sort_order'] === 4) {
                    return '';
                }
                return asset($card['default_image']);
            }
        }

        return asset('frontend/assets/images/os_wallclock.jpg');
    }

    private function withCacheBuster(string $url): string
    {
        $version = optional($this->image_changed_at)->timestamp ?? time();

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $version;
    }

    private static function ensureColumns(): void
    {
        if (
            Schema::hasColumn('home_highlights', 'icon') &&
            ! Schema::hasColumn('home_highlights', 'image')
        ) {
            Schema::table('home_highlights', function ($table) {
                $table->string('image')->nullable()->after('title');
            });
        }

        if (
            ! Schema::hasColumn('home_highlights', 'image') &&
            ! Schema::hasColumn('home_highlights', 'icon')
        ) {
            Schema::table('home_highlights', function ($table) {
                $table->string('image')->nullable()->after('title');
            });
        }

        if (! Schema::hasColumn('home_highlights', 'image_updated_at')) {
            Schema::table('home_highlights', function ($table) {
                $table->timestamp('image_updated_at')->nullable();
            });
        }
    }
}
