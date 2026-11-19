<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('benefits')) {
            Schema::create('benefits', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->longText('svg_logo')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

            return;
        }

        if (!Schema::hasColumn('benefits', 'svg_logo')) {
            Schema::table('benefits', function (Blueprint $table) {
                $table->longText('svg_logo')->nullable()->after('description');
            });
        } else {
            // Expand column so full SVG markup is stored (not truncated)
            DB::statement('ALTER TABLE benefits MODIFY svg_logo LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        //
    }
};
