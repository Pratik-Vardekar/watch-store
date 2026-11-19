<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'status',
        'gender',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public const GENDERS = [
        'women' => 'Women',
        'men' => 'Men',
        'both' => 'Both (Women & Men)',
    ];

    public function scopeForMenuGender($query, string $gender)
    {
        return $query->where(function ($q) use ($gender) {
            $q->where('gender', $gender)
                ->orWhere('gender', 'both')
                // Keep older records (no gender set) visible in both menus.
                ->orWhereNull('gender')
                ->orWhere('gender', '');
        });
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('backend/assets/images/category/' . $this->image);
        }

        return asset('frontend/assets/images/no-image.png');
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(
            Coupon::class,
            'coupon_category',
            'category_id',
            'coupon_id'
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Effective product price SQL: selling_price when set, otherwise MRP/price.
     */
    public static function productPriceSql(string $table = 'products'): string
    {
        return "COALESCE(NULLIF({$table}.selling_price, 0), NULLIF({$table}.price, 0))";
    }

    /**
     * Eager-load the lowest active product price for each category.
     */
    public function scopeWithStartingFromPrice(Builder $query): Builder
    {
        $priceSql = static::productPriceSql('products');

        return $query
            ->select($query->getModel()->getTable() . '.*')
            ->selectSub(
                Product::query()
                    ->selectRaw("MIN({$priceSql})")
                    ->whereColumn('products.category_id', $query->getModel()->getTable() . '.id')
                    ->where('products.status', 1)
                    ->whereRaw("{$priceSql} > 0"),
                'starting_from_price'
            );
    }

    /**
     * Minimum active product price in this category (selling price preferred).
     */
    public function getStartingFromPrice(): ?float
    {
        if (array_key_exists('starting_from_price', $this->attributes)) {
            $value = $this->attributes['starting_from_price'];

            return $value !== null ? (float) $value : null;
        }

        $priceSql = static::productPriceSql('products');

        $min = $this->products()
            ->where('status', 1)
            ->whereRaw("{$priceSql} > 0")
            ->selectRaw("MIN({$priceSql}) as min_price")
            ->value('min_price');

        return $min !== null ? (float) $min : null;
    }

    /**
     * Formatted "Starting from" price for display.
     */
    public function formattedStartingFromPrice(string $fallback = '—'): string
    {
        $price = $this->getStartingFromPrice();

        return $price !== null
            ? '₹' . number_format($price, 2)
            : $fallback;
    }
}
