<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HowToPlaySection extends Model
{
    protected $fillable = [
        'title',
        'youtube_url',
        'thumbnail',
        'button_text',
        'button_url',
    ];
}
