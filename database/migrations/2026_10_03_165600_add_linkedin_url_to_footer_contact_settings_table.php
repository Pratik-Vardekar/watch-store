<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('footer_contact_settings') && !Schema::hasColumn('footer_contact_settings', 'linkedin_url')) {
            Schema::table('footer_contact_settings', function (Blueprint $table) {
                $table->string('linkedin_url')->nullable()->after('youtube_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('footer_contact_settings') && Schema::hasColumn('footer_contact_settings', 'linkedin_url')) {
            Schema::table('footer_contact_settings', function (Blueprint $table) {
                $table->dropColumn('linkedin_url');
            });
        }
    }
};
