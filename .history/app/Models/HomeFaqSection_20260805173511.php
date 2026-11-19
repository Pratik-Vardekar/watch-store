<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFaqSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'status',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    // Default attribute values
    protected $attributes = [
        'title' => 'Frequently Asked Questions',
        'status' => 1,
        'sort_order' => 0,
    ];

    // Scope for active sections
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope for ordered sections
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Relationship with FAQs
    public function faqs()
    {
        return $this->hasMany(HomeFaq::class);
    }
}