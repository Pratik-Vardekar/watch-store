<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $now = now();

        DB::table('home_faqs')->insert([
            [
                'question' => 'What is The Word Hunt Challenge?',
                'answer' => 'The Word Hunt Challenge is an engaging word puzzle game designed to entertain and educate players. It helps improve vocabulary, problem-solving skills, and strategic thinking while providing a fun and interactive experience.',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'How does playing The Word Hunt Challenge improve vocabulary?',
                'answer' => 'The game introduces players to new words and challenges them to form words from scrambled letters. This enhances word recognition, spelling, and overall language skills in a fun and engaging way.',
                'sort_order' => 2,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Can I play The Word Hunt Challenge with friends and family?',
                'answer' => 'Yes! The game offers multiplayer modes where you can compete with friends and family. It\'s a great way to have fun together while sharpening your word skills.',
                'sort_order' => 3,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is The Word Hunt Challenge suitable for all age groups?',
                'answer' => 'Absolutely! The game is designed to be family-friendly, making it enjoyable for kids, teens, and adults alike. It provides different difficulty levels to suit players of all ages.',
                'sort_order' => 4,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'What makes The Word Hunt Challenge unique?',
                'answer' => 'Unlike traditional word games, The Word Hunt Challenge combines fun with education, offering an interactive way to boost vocabulary and cognitive skills. Its engaging gameplay and multiplayer options set it apart from other word puzzles.',
                'sort_order' => 5,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_faqs');
    }
};
