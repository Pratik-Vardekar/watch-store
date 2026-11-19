<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('foot_kids_image')->nullable();
            $table->string('foot_kids_shadow_image')->nullable();
            $table->string('form_title')->nullable();
            $table->string('form_subtitle')->nullable();
            $table->string('enroll_title_small')->nullable();
            $table->string('enroll_title_highlight')->nullable();
            $table->string('enroll_title_suffix')->nullable();
            $table->string('enroll_ribbon_image')->nullable();
            $table->string('enroll_product_image')->nullable();
            $table->string('enroll_button_text')->nullable();
            $table->string('enroll_button_url')->nullable();
            $table->string('connect_tag_image')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('amazon_url')->nullable();
            $table->string('amazon_logo_image')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_contact_settings');
    }
};
