<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('review');
            $table->decimal('rating', 2, 1)->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $now = now();

        DB::table('home_testimonials')->insert([
            [
                'name' => 'Drashti Patel',
                'review' => 'Finally, a game that pulls us away from screens! WATCH board games are the perfect blend of fun and learning. We won\'t even realise that we\'re improving vocabulary, focus, and problem-solving skills while playing. It\'s engaging, simple to understand, and great for family bonding. A must-have for every home that values smart play.',
                'rating' => 4.5,
                'sort_order' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_testimonials');
    }
};
