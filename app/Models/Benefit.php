<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'svg_logo',   // Only SVG logo
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}