<?php
// app/Models/BuyNow.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyNow extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'variant_id',
        'quantity',
        'address_id',
        'coupon_code',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    public function address()
{
    return $this->belongsTo(Address::class);
}
}