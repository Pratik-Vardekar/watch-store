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
        'status'
    ];
   public function category()
{
    return $this->belongsTo(BlogCategory::class, 'category_id');
}
}