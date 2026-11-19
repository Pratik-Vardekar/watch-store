<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpCategory extends Model
{
    protected $fillable = [
        'title',
        'color',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const COLORS = [
        'yellow' => 'Yellow',
        'teal' => 'Teal',
        'purple' => 'Purple',
        'orange' => 'Orange',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(HelpFaq::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeFaqs(): HasMany
    {
        return $this->faqs()->where('status', 1);
    }

    public function getBgClassAttribute(): string
    {
        return match ($this->color) {
            'teal' => 'bgTeal',
            'purple' => 'bgPurple',
            'orange' => 'bgOrange',
            default => 'bgYellow',
        };
    }

    public function getBgSubClassAttribute(): string
    {
        return match ($this->color) {
            'teal' => 'bgTealSub',
            'purple' => 'bgPurpleSub',
            'orange' => 'bgOrangeSub',
            default => 'bgYellowSub',
        };
    }
}
