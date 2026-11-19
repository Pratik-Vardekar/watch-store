<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'flat',
        'area',
        'landmark',
        'city',
        'state',
        'country',
        'pincode',
        'type',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $street = $this->address;

        if (!$street && ($this->flat || $this->area)) {
            $street = trim(($this->flat ?? '') . ', ' . ($this->area ?? ''), ', ');
        }

        return collect([
            $street,
            $this->landmark,
            $this->city,
            $this->state,
            $this->pincode,
            $this->country ?: 'India',
        ])->filter()->implode(', ');
    }
}