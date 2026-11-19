<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFaq extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'question',
        'answer',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}