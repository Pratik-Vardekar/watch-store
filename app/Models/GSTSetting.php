<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GSTSetting extends Model
{
    protected $table = 'gst_settings';

    protected $fillable = [
        'gst_percentage',
        'status'
    ];
}