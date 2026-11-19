<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFaq extends Model
{
    protected $table = 'home_faqs';

    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'status',
        'image',
        'created_at',
        'updated_at',
    ];

    // Default attribute values
    protected $attributes = [
        'status' => 1,
        'sort_order' => 0,
    ];

    // Scope for active FAQs
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope for ordered FAQs
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // Relationship with section (if you have foreign key)
    public function section()
    {
        return $this->belongsTo(HomeFaqSection::class, 'home_faq_section_id');
    }
}