<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('footer_contact_settings', function (Blueprint $table) {
            // Kids images
            $table->string('foot_kids_image')->nullable()->after('contact_address');
            $table->string('foot_kids_shadow_image')->nullable()->after('foot_kids_image');

            // Reach out form
            $table->string('form_title')->nullable()->after('foot_kids_shadow_image');
            $table->string('form_subtitle')->nullable()->after('form_title');

            // Enroll banner
            $table->string('enroll_title_small')->nullable()->after('form_subtitle');
            $table->string('enroll_title_highlight')->nullable()->after('enroll_title_small');
            $table->string('enroll_title_suffix')->nullable()->after('enroll_title_highlight');
            $table->string('enroll_ribbon_image')->nullable()->after('enroll_title_suffix');
            $table->string('enroll_product_image')->nullable()->after('enroll_ribbon_image');
            $table->string('enroll_button_text')->nullable()->after('enroll_product_image');
            $table->string('enroll_button_url')->nullable()->after('enroll_button_text');

            // Connect tag
            $table->string('connect_tag_image')->nullable()->after('enroll_button_url');

            // Social + Amazon
            $table->string('facebook_url')->nullable()->after('connect_tag_image');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->string('amazon_url')->nullable()->after('youtube_url');
            $table->string('amazon_logo_image')->nullable()->after('amazon_url');

            // Copyright
            $table->string('copyright_text')->nullable()->after('amazon_logo_image');
        });
    }

    public function down(): void
    {
        Schema::table('footer_contact_settings', function (Blueprint $table) {
            $table->dropColumn([
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
                'instagram_url',
                'youtube_url',
                'amazon_url',
                'amazon_logo_image',
                'copyright_text',
            ]);
        });
    }
};
