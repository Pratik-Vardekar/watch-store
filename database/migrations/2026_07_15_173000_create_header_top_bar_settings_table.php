<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_top_bar_settings', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_message')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        DB::table('header_top_bar_settings')->insert([
            'delivery_message' => 'Get free home delivery (Order more than ₹300)',
            'email' => 'watchinnovativeventures@gmail.com',
            'address' => 'Santacruz West, Mumbai 400 054',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('header_top_bar_settings');
    }
};
