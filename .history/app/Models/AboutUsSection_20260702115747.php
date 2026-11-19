<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUsSection extends Model
{
    protected $fillable = [
        'title',
        'content',
        'tag_1',
        'tag_2',
        'tag_3',
        'position',
        'stats',
        'whoweare_subtitle',
        'whoweare_title',
        'whoweare_content',
        'features',
        'mission',
        'vision',
        'core_values',
        'team',
        'certificates',
    ];

    protected $casts = [
        'stats' => 'array',
        'features' => 'array',
        'team' => 'array',
        'certificates' => 'array',
    ];
}
