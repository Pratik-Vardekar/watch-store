<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFaqSection extends Model
{
    protected $fillable = [
        'image',
    ];

    /**
     * Public URL for the FAQ side image (uploaded or default).
     */
    public function imageUrl(): string
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset('frontend/assets/images/faqKid.png');
    }
}
