<?php
namespace App\Models;
use App\Models\GalleryImage;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'content',
        'image',
        'status'
    ];

    public function images()
    {
        return $this->hasMany(GalleryImage::class);
    }
}