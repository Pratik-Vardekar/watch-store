<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('brands')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'gender')) {
                $table->string('gender', 20)->nullable()->after('brand_id');
            }
        });

        // Category is no longer required on product add (brand-wise flow).
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand_id')) {
                $table->dropConstrainedForeignId('brand_id');
            }
            if (Schema::hasColumn('products', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
