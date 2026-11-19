<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [

        'order_id',
        'type',

        'name',
        'phone',
        'alternate_phone',

        'company_name',
        'gstin',

        'address',
        'landmark',

        'city',
        'state',
        'country',
        'pincode',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }
}