<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('color', 20)->default('yellow');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('help_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('help_category_id')->constrained('help_categories')->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $now = now();

        $categories = [
            ['title' => 'Orders', 'color' => 'yellow', 'sort_order' => 1],
            ['title' => 'Returns & Exchanges', 'color' => 'teal', 'sort_order' => 2],
            ['title' => 'Shipping and Delivery', 'color' => 'purple', 'sort_order' => 3],
            ['title' => 'Product Info', 'color' => 'orange', 'sort_order' => 4],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = DB::table('help_categories')->insertGetId([
                'title' => $category['title'],
                'color' => $category['color'],
                'sort_order' => $category['sort_order'],
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $sampleAnswer = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porta interdum lacus sagittis gravida. Sed a vehicula libero. Quisque ante nisi, gravida id rhoncus non, aliquam sit amet lacus.';

        $faqs = [
            $categoryIds[0] => [
                'Sed quis facilisis enim.',
                'Tincidunt vitae euismod at, pulvinar condimentum est.',
                'Ex nunc, tincidunt vitae euismod at, pulvinar condimentum est.',
                'Quisque ex nunc, tincidunt vitae euismod at, pulvinar condimentum est.',
            ],
            $categoryIds[1] => [
                'Pellentesque sodales ullamcorper nibh, ac viverra enim.',
                'Proin rutrum ante quis metus gravida, eget dignissim neque congue.',
                'Vestibulum mauris elit, bibendum ac ante sit amet, ultrices finibus dui.',
                'Maecenas vitae ullamcorper ligula.',
            ],
            $categoryIds[2] => [
                'Sed quis facilisis enim.',
                'Tincidunt vitae euismod at, pulvinar condimentum est.',
                'Ex nunc, tincidunt vitae euismod at, pulvinar condimentum est.',
                'Quisque ex nunc, tincidunt vitae euismod at, pulvinar condimentum est.',
            ],
            $categoryIds[3] => [
                'Pellentesque sodales ullamcorper nibh, ac viverra enim.',
                'Proin rutrum ante quis metus gravida, eget dignissim neque congue.',
                'Vestibulum mauris elit, bibendum ac ante sit amet, ultrices finibus dui.',
                'Maecenas vitae ullamcorper ligula.',
            ],
        ];

        foreach ($faqs as $categoryId => $questions) {
            foreach ($questions as $index => $question) {
                DB::table('help_faqs')->insert([
                    'help_category_id' => $categoryId,
                    'question' => $question,
                    'answer' => $sampleAnswer,
                    'sort_order' => $index + 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('help_faqs');
        Schema::dropIfExists('help_categories');
    }
};
