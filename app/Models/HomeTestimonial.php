<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeTestimonial extends Model
{
    protected $fillable = [
        'name',
        'review',
        'rating',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'rating' => 'float',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getStarClassAttribute(): string
    {
        $rating = max(1, min(5, (float) $this->rating));

        if ($rating == floor($rating)) {
            return 'star' . (int) $rating;
        }

        return 'star' . floor($rating) . 'h';
    }
}
