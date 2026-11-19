<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('sliders')) {
            Schema::table('sliders', function (Blueprint $table) {
                if (! Schema::hasColumn('sliders', 'mobile_image')) {
                    $table->string('mobile_image')->nullable()->after('image');
                }
                if (! Schema::hasColumn('sliders', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('status');
                }
                if (! Schema::hasColumn('sliders', 'subtitle')) {
                    $table->string('subtitle')->nullable()->after('title');
                }
                if (! Schema::hasColumn('sliders', 'link')) {
                    $table->text('link')->nullable()->after('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sliders')) {
            Schema::table('sliders', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('sliders', 'mobile_image')) {
                    $columnsToDrop[] = 'mobile_image';
                }
                if (Schema::hasColumn('sliders', 'sort_order')) {
                    $columnsToDrop[] = 'sort_order';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};

