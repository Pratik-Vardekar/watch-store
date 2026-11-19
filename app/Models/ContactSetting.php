<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'contact_address',
        'contact_phone',
        'contact_email',
        'business_days',
        'business_hours',
        'support_message',
        'support_timings',
    ];
}