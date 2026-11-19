<?php

namespace App\Models;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [

        'title',    
        'slug',
        'category_id',
        'image',
        'author',
        'blog_date',
        'short_description',
        'description',  
        'status',
        'tags',
        'trending'
    ];
   public function category()
{
    return $this->belongsTo(BlogCategory::class, 'category_id');
}
}