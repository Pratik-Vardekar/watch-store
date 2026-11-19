<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterContactSetting extends Model
{
    protected $table = 'footer_contact_settings';

    protected $fillable = [
        'contact_phone',
        'contact_email',
        'contact_address',
    ];
}
