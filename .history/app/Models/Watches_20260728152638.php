<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watches extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'recommended_size',
        'sort_order',
        'status',
    ];
}
