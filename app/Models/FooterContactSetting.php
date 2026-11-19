<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterContactSetting extends Model
{
    protected $table = 'footer_contact_settings';

    protected $fillable = [
        'contact_phone',
        'contact_email',
        'contact_address',
        'foot_kids_image',
        'foot_kids_shadow_image',
        'form_title',
        'form_subtitle',
        'enroll_title_small',
        'enroll_title_highlight',
        'enroll_title_suffix',
        'enroll_ribbon_image',
        'enroll_product_image',
        'enroll_button_text',
        'enroll_button_url',
        'connect_tag_image',
        'facebook_url',
        'twitter_url',
        'whatsapp_number',
        'instagram_url',
        'youtube_url',
        'linkedin_url',
        'amazon_url',
        'amazon_logo_image',
        'copyright_text',
    ];
}
