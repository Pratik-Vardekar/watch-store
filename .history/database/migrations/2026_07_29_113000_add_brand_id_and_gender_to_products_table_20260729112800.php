<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('products', 'gender')) {
                $table->string('gender', 20)->nullable()->after('brand_id');
            }
        });

        // Add FK only if missing
        $fkExists = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'products'
              AND COLUMN_NAME = 'brand_id'
              AND REFERENCED_TABLE_NAME = 'brands'
        "))->isNotEmpty();

        if (! $fkExists && Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('brand_id')
                    ->references('id')
                    ->on('brands')
                    ->nullOnDelete();
            });
        }

        // Make category_id nullable without doctrine/dbal
        if (Schema::hasColumn('products', 'category_id')) {
            DB::statement('ALTER TABLE `products` MODIFY `category_id` BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $fkExists = collect(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'products'
                  AND COLUMN_NAME = 'brand_id'
                  AND REFERENCED_TABLE_NAME = 'brands'
            "))->isNotEmpty();

            if ($fkExists) {
                $table->dropForeign(['brand_id']);
            }

            if (Schema::hasColumn('products', 'brand_id')) {
                $table->dropColumn('brand_id');
            }

            if (Schema::hasColumn('products', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
