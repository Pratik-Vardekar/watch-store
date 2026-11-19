<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('sub_categories', 'show_in_explore')) {
                $table->tinyInteger('show_in_explore')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            if (Schema::hasColumn('sub_categories', 'show_in_explore')) {
                $table->dropColumn('show_in_explore');
            }
        });
    }
};
