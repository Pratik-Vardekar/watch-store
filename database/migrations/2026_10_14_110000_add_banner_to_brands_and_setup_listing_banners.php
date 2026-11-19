<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add banner column to brands table if missing
        if (Schema::hasTable('brands')) {
            if (! Schema::hasColumn('brands', 'banner')) {
                Schema::table('brands', function (Blueprint $table) {
                    $table->string('banner')->nullable()->after('logo');
                });
            }
        }

        // 2. Ensure home_banners table exists and has recommended_size column
        if (Schema::hasTable('home_banners')) {
            if (! Schema::hasColumn('home_banners', 'recommended_size')) {
                Schema::table('home_banners', function (Blueprint $table) {
                    $table->string('recommended_size')->nullable()->after('image');
                });
            }

            // Seed / update default listing banners (Recommended size: 2513 × 400 px)
            $now = now();
            $defaultBanners = [
                [
                    'slug' => 'men',
                    'name' => 'Men Watches Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 1,
                    'status' => 1,
                ],
                [
                    'slug' => 'women',
                    'name' => 'Women Watches Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 2,
                    'status' => 1,
                ],
                [
                    'slug' => 'brands',
                    'name' => 'All Brands Page Banner',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 3,
                    'status' => 1,
                ],
                [
                    'slug' => 'default',
                    'name' => 'All Watches (Default Listing Banner)',
                    'recommended_size' => '2513 × 400 px',
                    'sort_order' => 4,
                    'status' => 1,
                ],
            ];

            foreach ($defaultBanners as $item) {
                $existing = DB::table('home_banners')->where('slug', $item['slug'])->first();
                if (! $existing) {
                    DB::table('home_banners')->insert(array_merge($item, [
                        'image' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                } else {
                    DB::table('home_banners')
                        ->where('slug', $item['slug'])
                        ->update([
                            'name' => $existing->name ?: $item['name'],
                            'recommended_size' => '2513 × 400 px',
                            'sort_order' => $item['sort_order'],
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands') && Schema::hasColumn('brands', 'banner')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropColumn('banner');
            });
        }

        if (Schema::hasTable('home_banners') && Schema::hasColumn('home_banners', 'recommended_size')) {
            Schema::table('home_banners', function (Blueprint $table) {
                $table->dropColumn('recommended_size');
            });
        }
    }
};
