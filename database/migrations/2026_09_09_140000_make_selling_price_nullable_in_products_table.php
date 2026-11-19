<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'selling_price')) {
            DB::statement('ALTER TABLE `products` MODIFY `selling_price` DOUBLE(10, 2) NULL DEFAULT NULL');
        }
        if (Schema::hasColumn('products', 'sku')) {
            DB::statement('ALTER TABLE `products` MODIFY `sku` VARCHAR(255) NULL DEFAULT NULL');
        }
        if (Schema::hasColumn('products', 'model_no')) {
            DB::statement('ALTER TABLE `products` MODIFY `model_no` VARCHAR(255) NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'selling_price')) {
            DB::statement('ALTER TABLE `products` MODIFY `selling_price` DOUBLE(10, 2) NOT NULL DEFAULT 0.00');
        }
    }
};
