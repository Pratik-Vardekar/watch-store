<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'timing',
        'google_maps_link',
        'latitude',
        'longitude',
        'images',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public static function normalizeImageFilenames($images): array
    {
        if (is_string($images) && $images !== '') {
            $decoded = json_decode($images, true);
            $images = json_last_error() === JSON_ERROR_NONE ? $decoded : [$images];
        }

        if (! is_array($images)) {
            return [];
        }

        $filenames = [];

        foreach ($images as $image) {
            if (is_array($image)) {
                $image = $image['path']
                    ?? $image['url']
                    ?? $image['name']
                    ?? $image['filename']
                    ?? (count($image) === 1 ? reset($image) : null);
            }

            if (! is_string($image)) {
                continue;
            }

            $image = trim(str_replace('\\', '/', $image));

            if ($image === '' || $image === '[]' || $image === '{}') {
                continue;
            }

            $filenames[] = basename($image);
        }

        return array_values(array_unique($filenames));
    }

    public function getImageFilenamesAttribute(): array
    {
        return static::normalizeImageFilenames($this->images);
    }

    public function getImageUrlsAttribute(): array
    {
        // Use asset() with the correct path - automatically handles /watch-store/ subdirectory
        return array_map(
            fn (string $name) => asset('uploads/stores/' . $name),
            $this->image_filenames
        );
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->image_urls[0] ?? null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = $store->generateUniqueSlug($store->name);
            }
        });

        static::updating(function ($store) {
            if ($store->isDirty('name')) {
                $store->slug = $store->generateUniqueSlug($store->name, $store->id);
            }
        });
    }

    public function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public function slugExists($slug, $excludeId = null)
    {
        $query = static::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    // Accessor for primary image
    public function getPrimaryImageAttribute()
    {
        return $this->image_filenames[0] ?? null;
    }

    // Get all images
    public function getAllImagesAttribute()
    {
        return $this->image_filenames;
    }
}