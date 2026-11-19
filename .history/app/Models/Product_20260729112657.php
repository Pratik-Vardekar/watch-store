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
        return $query->where(function ($q) use ($gender) {
            $q->where('gender', $gender)->orWhere('gender', 'both');
        });
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
}