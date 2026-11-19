<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    public const EXPLORE_MENU_LIMIT = 4;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'image',
        'status',
        'show_in_explore',
    ];

    protected $casts = [
        'status' => 'integer',
        'show_in_explore' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('backend/assets/images/subcategory/' . $this->image);
        }

        return asset('frontend/assets/images/no-image.png');
    }

    public function scopeForExploreMenu($query)
    {
        return $query->where('status', 1)
            ->where('show_in_explore', 1)
            ->orderBy('id')
            ->limit(self::EXPLORE_MENU_LIMIT);
    }
}