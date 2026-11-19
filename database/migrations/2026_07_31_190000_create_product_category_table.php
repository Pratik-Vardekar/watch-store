<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
        });

        // Backfill existing primary category assignments into the pivot.
        $now = now();
        DB::table('products')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->chunkById(200, function ($products) use ($now) {
                $rows = [];
                foreach ($products as $product) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if (! empty($rows)) {
                    DB::table('product_category')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category');
    }
};
