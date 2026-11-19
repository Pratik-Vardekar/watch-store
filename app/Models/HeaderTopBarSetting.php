<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderTopBarSetting extends Model
{
    protected $table = 'header_top_bar_settings';

    protected $fillable = [
        'delivery_message',
        'email',
        'address',
    ];
}
