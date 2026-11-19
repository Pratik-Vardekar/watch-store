<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnFunSection extends Model
{
    protected $fillable = [
        'subtitle',
        'title',
        'title_highlight',
        'content',
        'button_text',
        'button_url',
        'image',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
