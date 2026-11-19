<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderMenuSetting extends Model
{
    protected $table = 'header_menu_settings';

    protected $fillable = [
        'brands_image',
        'women_image',
        'men_image',
        'about_image',
    ];
}

