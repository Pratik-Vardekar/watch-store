<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_us_sections', function (Blueprint $table) {
            // Stats section (array of {value, label})
            $table->json('stats')->nullable()->after('position');

            // "Who We Are" section
            $table->string('whoweare_subtitle')->nullable()->after('stats');
            $table->string('whoweare_title')->nullable()->after('whoweare_subtitle');
            $table->longText('whoweare_content')->nullable()->after('whoweare_title');
            $table->json('features')->nullable()->after('whoweare_content');

            // Mission / Vision / Values
            $table->longText('mission')->nullable()->after('features');
            $table->longText('vision')->nullable()->after('mission');
            $table->longText('core_values')->nullable()->after('vision');

            // Team (array of {name, role, desc, image})
            $table->json('team')->nullable()->after('core_values');

            // Certificates (array of {label, image, pdf})
            $table->json('certificates')->nullable()->after('team');
        });
    }

    public function down(): void
    {
        Schema::table('about_us_sections', function (Blueprint $table) {
            $table->dropColumn([
                'stats',
                'whoweare_subtitle',
                'whoweare_title',
                'whoweare_content',
                'features',
                'mission',
                'vision',
                'core_values',
                'team',
                'certificates',
            ]);
        });
    }
};
