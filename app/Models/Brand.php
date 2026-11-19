<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'banner',
        'description',
        'link',
        'status',
        'position',
    ];

    protected $appends = ['logo_url', 'banner_url', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug) && ! empty($brand->name)) {
                $brand->slug = static::generateUniqueSlug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->slug)) {
                $brand->slug = static::generateUniqueSlug($brand->name, $brand->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name) ?: 'brand';
        $slug = $baseSlug;
        $counter = 1;

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('brands', 'slug')) {
                $query = static::where('slug', $slug);
                if ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                }
                while ($query->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                    $query = static::where('slug', $slug);
                    if ($excludeId) {
                        $query->where('id', '!=', $excludeId);
                    }
                }
            }
        } catch (\Throwable $e) {
            return \Illuminate\Support\Str::slug($name);
        }

        return $slug;
    }

    public function getSlugAttribute()
    {
        return ! empty($this->attributes['slug'])
            ? $this->attributes['slug']
            : \Illuminate\Support\Str::slug($this->name);
    }

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

    public function getBannerUrlAttribute(): ?string
    {
        if (empty($this->attributes['banner'] ?? null)) {
            return null;
        }

        $banner = trim((string) $this->attributes['banner']);

        if (str_starts_with($banner, 'http://') || str_starts_with($banner, 'https://')) {
            return $banner;
        }

        $clean = ltrim(str_replace('\\', '/', $banner), '/');
        $relativePath = str_starts_with($clean, 'backend/') ? $clean : 'backend/assets/images/brands/' . $clean;

        $baseFile = base_path($relativePath);
        $publicFile = public_path($relativePath);

        $hasBase = file_exists($baseFile) && is_file($baseFile);
        $hasPublic = file_exists($publicFile) && is_file($publicFile);

        if ($hasBase || $hasPublic) {
            if ($hasBase && ! $hasPublic) {
                $dir = dirname($publicFile);
                if (! file_exists($dir)) {
                    @mkdir($dir, 0755, true);
                    @chmod($dir, 0755);
                }
                @copy($baseFile, $publicFile);
                @chmod($publicFile, 0644);
            } elseif ($hasPublic && ! $hasBase) {
                $dir = dirname($baseFile);
                if (! file_exists($dir)) {
                    @mkdir($dir, 0755, true);
                    @chmod($dir, 0755);
                }
                @copy($publicFile, $baseFile);
                @chmod($baseFile, 0644);
            }

            return asset($relativePath);
        }

        return null;
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