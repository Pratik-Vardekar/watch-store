<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const GENDERS = [
        'women' => 'Women',
        'men' => 'Men',
        'both' => 'Both (Women & Men)',
    ];

    protected $fillable = [
        'brand_id',
        'gender',
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'thumbnail',
        'price',
        'selling_price',
        'details',
        'description',
        'specification',
        'quantity',
        'gallery',
        'gallery_order',
        'status',
        'is_trending',
        'is_featured',
        'is_best_selling',
        'is_top_rated',
        'is_new_arrival',
        'badge',
        'discount',
        'sku',
        'product_type',
        'gst_setting_id',
        'video',
    ];

    public const PRODUCT_IMAGE_DIR = 'backend/assets/images/product/';
    public const FALLBACK_IMAGE = 'frontend/assets/images/product_1.jpg';

    protected $casts = [
        'price' => 'float',
        'selling_price' => 'float',
        'is_trending' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_selling' => 'boolean',
        'is_top_rated' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function gstSetting()
    {
        return $this->belongsTo(GSTSetting::class, 'gst_setting_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category')
            ->withTimestamps();
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Products linked via primary category_id, product_category pivot,
     * or a subcategory that belongs to the category.
     */
    public function scopeInCategories($query, array $categoryIds)
    {
        $categoryIds = array_values(array_unique(array_filter(array_map('intval', $categoryIds))));

        if (empty($categoryIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds)
                ->orWhereHas('categories', function ($rel) use ($categoryIds) {
                    $rel->whereIn('categories.id', $categoryIds);
                })
                ->orWhereHas('subcategory', function ($rel) use ($categoryIds) {
                    $rel->whereIn('sub_categories.category_id', $categoryIds);
                });
        });
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->inCategories([(int) $categoryId]);
    }

    public function scopeInSubcategory($query, $subcategoryId)
    {
        $subcategoryId = (int) $subcategoryId;

        if ($subcategoryId <= 0) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('subcategory_id', $subcategoryId);
    }

    /**
     * All category IDs for this product (pivot + primary fallback).
     */
    public function allCategoryIds(): array
    {
        $ids = $this->relationLoaded('categories')
            ? $this->categories->pluck('id')->map(fn ($id) => (int) $id)->all()
            : $this->categories()->pluck('categories.id')->map(fn ($id) => (int) $id)->all();

        if ($this->category_id) {
            $ids[] = (int) $this->category_id;
        }

        return array_values(array_unique($ids));
    }

    public function scopeForMenuGender($query, string $gender)
    {
        $table = $query->getModel()->getTable();

        return $query->where(function ($q) use ($gender, $table) {
            $q->where("{$table}.gender", $gender)
                ->orWhere("{$table}.gender", 'both');
        });
    }

    public function scopeWithAssignedGender($query)
    {
        $table = $query->getModel()->getTable();

        return $query->whereNotNull("{$table}.gender")
            ->where("{$table}.gender", '!=', '');
    }

    public function scopeForBothGender($query)
    {
        $table = $query->getModel()->getTable();

        return $query->where("{$table}.gender", 'both');
    }

    public function getGenderAttribute($value): ?string
    {
        $gender = is_string($value) ? strtolower(trim($value)) : $value;

        return ($gender === '' || $gender === null) ? null : $gender;
    }

    public function setGenderAttribute($value): void
    {
        $gender = is_string($value) ? strtolower(trim($value)) : $value;

        $this->attributes['gender'] = ($gender === '' || $gender === null)
            ? null
            : $gender;
    }

    public static function calculateDiscountPercent(float $price, float $sellingPrice, $fallback = null): int
    {
        if ($price > 0 && $sellingPrice >= 0 && $sellingPrice < $price) {
            return (int) round((($price - $sellingPrice) / $price) * 100);
        }

        if ($fallback !== null && $fallback !== '') {
            return (int) $fallback;
        }

        return 0;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Average Rating
    |--------------------------------------------------------------------------
    */

    public function averageRating()
    {
        return round(
            $this->reviews()
                ->where('status', 1)
                ->avg('rating'),
            1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Total Reviews
    |--------------------------------------------------------------------------
    */

    public function totalReviews()
    {
        return $this->reviews()
            ->where('status', 1)
            ->count();
    }
    public function carts()
{
    return $this->hasMany(Cart::class);
}

public function wishlists()
{
    return $this->hasMany(Wishlist::class);
}
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}
 public function faqs()
    {
        return $this->hasMany(ProductFaq::class)->orderBy('order');
    }

    public function getSalePriceAttribute()
    {
        $selling = (float) ($this->attributes['selling_price'] ?? 0);
        $price = (float) ($this->attributes['price'] ?? 0);

        return $selling > 0 ? $selling : $price;
    }

    public function getOriginalPriceAttribute()
    {
        return (float) ($this->attributes['price'] ?? 0);
    }

    public function getIsNewAttribute()
    {
        return (bool) ($this->attributes['is_new_arrival'] ?? false);
    }

    public function getGalleryAttribute($value): array
    {
        return $this->decodeImageList($value);
    }

    public function setGalleryAttribute($value): void
    {
        $this->attributes['gallery'] = json_encode($this->decodeImageList($value));
    }

    public function getGalleryOrderAttribute($value): array
    {
        return $this->decodeImageList($value);
    }

    public function setGalleryOrderAttribute($value): void
    {
        $this->attributes['gallery_order'] = json_encode($this->decodeImageList($value));
    }

    public function getProductImageUrl($image = null): string
    {
        return $this->absoluteImageUrl($image ?: $this->rawThumbnail(), true);
    }

    public function thumbnailUrl(): string
    {
        return $this->absoluteImageUrl($this->rawThumbnail());
    }

    public function galleryUrls(): array
    {
        $urls = [];

        foreach ($this->getSliderImages() as $img) {
            $urls[] = $this->absoluteImageUrl($img);
        }

        if (empty($urls)) {
            $urls[] = static::fallbackImageUrl();
        }

        return array_values(array_unique($urls));
    }

    public function applyApiMedia(): static
    {
        $gallery = $this->galleryUrls();

        $this->thumbnail = $this->thumbnailUrl();
        $this->gallery = $gallery;
        $this->gallery_order = $gallery;

        return $this;
    }

    public static function fallbackImageUrl(): string
    {
        return url(self::FALLBACK_IMAGE);
    }

    public function getSliderImages(): array
    {
        $images = [];
        $thumbnail = $this->rawThumbnail();

        if ($thumbnail) {
            $images[] = $thumbnail;
        }

        foreach (array_merge($this->gallery, $this->gallery_order) as $img) {
            if ($img && ! in_array($img, $images, true)) {
                $images[] = $img;
            }
        }

        return $images;
    }

    private function rawThumbnail(): ?string
    {
        $file = $this->attributes['thumbnail'] ?? null;

        return is_string($file) && $file !== '' ? $file : null;
    }

    private function decodeImageList($value): array
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $value = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, function ($img) {
            return is_string($img) && $img !== '';
        }));
    }

    private function absoluteImageUrl(?string $file, bool $useAsset = false): string
    {
        if (is_string($file) && $file !== '') {
            if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
                return $file;
            }

            $path = self::PRODUCT_IMAGE_DIR . ltrim($file, '/');

            return $useAsset ? asset($path) : url($path);
        }

        return $useAsset ? asset(self::FALLBACK_IMAGE) : static::fallbackImageUrl();
    }

    public function getGstRate(): float
    {
        // If product has specific GST setting
        if ($this->gst_setting_id && $this->gstSetting) {
            return (float) $this->gstSetting->rate;
        }
        
        // Default to 18%
        return 18.0;
    }
    public function getInclusiveTaxInfo($price): array
    {
        $gstRate = $this->getGstRate();
        $price = (float) $price;
        
        // GST Inclusive Formula: Tax = (Price × Rate) / (100 + Rate)
        $taxAmount = ($price * $gstRate) / (100 + $gstRate);
        $basePrice = $price - $taxAmount;
        
        return [
            'gst_percentage' => $gstRate,
            'tax_amount' => round($taxAmount, 2),
            'base_price' => round($basePrice, 2),
            'inclusive' => true,
        ];
    }

    public function getPriceWithGstBreakdown(): array
    {
        $sellingPrice = (float) ($this->selling_price ?? $this->price ?? 0);
        $gstRate = $this->getGstRate();
        
        $taxAmount = ($sellingPrice * $gstRate) / (100 + $gstRate);
        
        return [
            'selling_price' => $sellingPrice,
            'gst_rate' => $gstRate,
            'gst_amount' => round($taxAmount, 2),
            'base_price' => round($sellingPrice - $taxAmount, 2),
        ];
    }

}