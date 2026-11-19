<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at',

        // NEW (important for accounting)
        'type',        // payment | refund | adjustment
        'reference',   // UTR / gateway ref / manual ref
        'note',
        'payment_stage',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}