<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_menu_settings', function (Blueprint $table) {
            $table->id();

            // Header dropdown menu images
            $table->string('brands_image')->nullable();
            $table->string('women_image')->nullable();
            $table->string('men_image')->nullable();
            $table->string('about_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_menu_settings');
    }
};

