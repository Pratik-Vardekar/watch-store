<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFaqSection extends Model
{
    protected $table = 'home_faq_sections';

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    // Default attribute values
    protected $attributes = [
        'status' => 1,
    ];

    // Scope for active sections
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope for ordered sections
    public function scopeOrdered($query)
    {
        return $query->orderBy('id');
    }

    // Relationship with FAQs
    public function faqs()
    {
        return $this->hasMany(HomeFaq::class);
    }
}