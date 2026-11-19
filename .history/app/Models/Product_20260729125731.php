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

   protected $casts = [
    'price' => 'float',
    'selling_price' => 'float',
    'gallery' => 'array',
    'gallery_order' => 'array',

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

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function scopeForMenuGender($query, string $gender)
    {
        $table = $query->getModel()->getTable();

        return $query->where(function ($q) use ($gender, $table) {
            $q->where("{$table}.gender", $gender)->orWhere("{$table}.gender", 'both');
        });
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

    public function getProductImageUrl($image = null): string
    {
        $file = $image ?: $this->thumbnail;
        if ($file) {
            return asset('backend/assets/images/product/' . $file);
        }

        return asset('frontend/assets/images/product_1.jpg');
    }

    public function getSliderImages(): array
    {
        $images = [];

        if ($this->thumbnail) {
            $images[] = $this->thumbnail;
        }

        $gallery = is_array($this->gallery)
            ? $this->gallery
            : (is_array($this->gallery_order) ? $this->gallery_order : []);

        foreach ($gallery as $img) {
            if ($img && ! in_array($img, $images, true)) {
                $images[] = $img;
            }
        }

        return $images;
    }
    public function getInclusiveTaxInfo($price)
{
    $price = (float) $price;

    $gst = $this->gstSetting;

    if (!$gst) {
        return [
            'gst_percentage' => 0,
            'tax_amount' => 0,
            'base_price' => $price,
            'inclusive_price' => $price,
        ];
    }

    // Change 'gst_percentage' if your GSTSetting model uses a different column name.
    $rate = (float) ($gst->gst_percentage ?? $gst->percentage ?? 0);

    if ($rate <= 0) {
        return [
            'gst_percentage' => 0,
            'tax_amount' => 0,
            'base_price' => $price,
            'inclusive_price' => $price,
        ];
    }

    $basePrice = $price / (1 + ($rate / 100));
    $taxAmount = $price - $basePrice;

    return [
        'gst_percentage' => $rate,
        'tax_amount' => round($taxAmount, 2),
        'base_price' => round($basePrice, 2),
        'inclusive_price' => round($price, 2),
    ];
}

}