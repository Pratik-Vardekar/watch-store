<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class HomeBanner extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'sort_order',
        'recommended_size',
        'status',
    ];

    protected $appends = ['image_url'];

    /**
     * Resolves the actual accessible URL of the banner image.
     * Searches both base_path and public_path, auto-mirrors for shared hosting,
     * and returns null if the physical file does not exist.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image) || $this->image === 'null') {
            return null;
        }

        $image = trim((string) $this->image);

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $cleanPath = ltrim(str_replace('\\', '/', $image), '/');

        $candidates = [];
        if (str_starts_with($cleanPath, 'uploads/') || str_starts_with($cleanPath, 'backend/') || str_starts_with($cleanPath, 'frontend/')) {
            $candidates[] = $cleanPath;
        } else {
            $candidates[] = 'uploads/home-banners/' . $cleanPath;
            $candidates[] = 'backend/assets/images/' . $cleanPath;
            $candidates[] = 'frontend/assets/images/' . $cleanPath;
        }

        foreach ($candidates as $candidate) {
            $baseFile = base_path($candidate);
            $publicFile = public_path($candidate);

            $hasBase = file_exists($baseFile) && is_file($baseFile);
            $hasPublic = file_exists($publicFile) && is_file($publicFile);

            if ($hasBase || $hasPublic) {
                // Ensure mirrored across both base_path and public_path for Apache/cPanel compatibility
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

                return asset($candidate);
            }
        }

        return null;
    }

    /**
     * Get banner URL by slug, with fallback to default system banner.
     */
    public static function getBannerUrl(string $slug, ?string $fallback = null): string
    {
        $defaultFallback = $fallback ?: asset('frontend/assets/images/prodListBanner.jpg');

        try {
            $banner = static::where('status', 1)->where('slug', $slug)->first();
            if ($banner) {
                $url = $banner->image_url;
                if (! empty($url)) {
                    return $url;
                }
            }
        } catch (\Throwable $e) {
            return $defaultFallback;
        }

        return $defaultFallback;
    }

    /**
     * Auto-ensure that required listing banners and table schema exist.
     */
    public static function ensureDefaults(): void
    {
        try {
            if (! Schema::hasTable('home_banners')) {
                return;
            }

            if (! Schema::hasColumn('home_banners', 'recommended_size')) {
                Schema::table('home_banners', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('recommended_size')->nullable()->after('image');
                });
            }

            $defaults = [
                [
                    'slug' => 'men',
                    'name' => 'Men Watches Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 1,
                    'status' => 1,
                ],
                [
                    'slug' => 'women',
                    'name' => 'Women Watches Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 2,
                    'status' => 1,
                ],
                [
                    'slug' => 'brands',
                    'name' => 'All Brands Page Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 3,
                    'status' => 1,
                ],
                [
                    'slug' => 'default',
                    'name' => 'All Watches (Default Listing Banner)',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 4,
                    'status' => 1,
                ],
            ];

            foreach ($defaults as $item) {
                $banner = static::where('slug', $item['slug'])->first();
                if (! $banner) {
                    static::create($item);
                } else {
                    $banner->update([
                        'recommended_size' => '2513 × 400 px',
                        'sort_order' => $item['sort_order'],
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
}