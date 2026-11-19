<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('brands')) {
            if (! Schema::hasColumn('brands', 'slug')) {
                Schema::table('brands', function (Blueprint $table) {
                    $table->string('slug')->nullable()->after('name');
                });
            }

            // Populate slug for existing brands if empty
            $brands = DB::table('brands')->get();
            $usedSlugs = [];

            foreach ($brands as $brand) {
                $baseSlug = Str::slug($brand->name ?: 'brand-' . $brand->id) ?: ('brand-' . $brand->id);
                $slug = ! empty($brand->slug) ? $brand->slug : $baseSlug;
                $candidate = $slug;
                $counter = 1;

                while (in_array($candidate, $usedSlugs, true)) {
                    $candidate = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $usedSlugs[] = $candidate;

                DB::table('brands')
                    ->where('id', $brand->id)
                    ->update(['slug' => $candidate]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands') && Schema::hasColumn('brands', 'slug')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
