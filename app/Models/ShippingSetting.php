<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'free_shipping_amount',
        'shipping_charge',
        'status'
    ];
}