<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('footer_contact_settings') && !Schema::hasColumn('footer_contact_settings', 'whatsapp_number')) {
            Schema::table('footer_contact_settings', function (Blueprint $table) {
                $table->string('whatsapp_number')->nullable()->after('twitter_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('footer_contact_settings') && Schema::hasColumn('footer_contact_settings', 'whatsapp_number')) {
            Schema::table('footer_contact_settings', function (Blueprint $table) {
                $table->dropColumn('whatsapp_number');
            });
        }
    }
};
