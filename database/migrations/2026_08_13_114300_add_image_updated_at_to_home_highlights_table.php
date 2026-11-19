<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_highlights', function (Blueprint $table) {
            if (
                Schema::hasColumn('home_highlights', 'icon') &&
                ! Schema::hasColumn('home_highlights', 'image')
            ) {
                $table->renameColumn('icon', 'image');
            }
        });

        Schema::table('home_highlights', function (Blueprint $table) {
            if (Schema::hasColumn('home_highlights', 'image_updated_at')) {
                return;
            }

            if (Schema::hasColumn('home_highlights', 'image')) {
                $table->timestamp('image_updated_at')->nullable()->after('image');

                return;
            }

            $table->timestamp('image_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('home_highlights', function (Blueprint $table) {
            if (Schema::hasColumn('home_highlights', 'image_updated_at')) {
                $table->dropColumn('image_updated_at');
            }
        });
    }
};
