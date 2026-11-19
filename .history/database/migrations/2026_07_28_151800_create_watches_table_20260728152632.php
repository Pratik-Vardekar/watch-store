<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('recommended_size')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $now = now();

        DB::table('watches')->insert([
            [
                'name' => 'For Him',
                'slug' => 'for-him',
                'image' => 'frontend/assets/images/catg_Mens.jpg',
                'recommended_size' => '800x1000',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'For Her',
                'slug' => 'for-her',
                'image' => 'frontend/assets/images/catg_Womens.jpg',
                'recommended_size' => '800x1000',
                'sort_order' => 2,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('watches');
    }
};
