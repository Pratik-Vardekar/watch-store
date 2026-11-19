<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayFunSection extends Model
{
    protected $fillable = [
        'tagline',
        'headline',
        'button_text',
        'button_url',
        'image',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];
}
