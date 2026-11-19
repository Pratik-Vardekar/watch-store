<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watches', function (Blueprint $table) {
            if (! Schema::hasColumn('watches', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (! Schema::hasColumn('watches', 'recommended_size')) {
                $table->string('recommended_size')->nullable()->after('image');
            }
        });

        $rows = DB::table('watches')->orderBy('sort_order')->orderBy('id')->get();
        $defaults = [
            1 => ['slug' => 'for-him', 'name' => 'For Him', 'recommended_size' => '800x1000'],
            2 => ['slug' => 'for-her', 'name' => 'For Her', 'recommended_size' => '800x1000'],
        ];

        $usedSlugs = [];

        foreach ($rows as $index => $row) {
            $position = $index + 1;
            $payload = $defaults[$position] ?? [
                'slug' => Str::slug($row->name ?: 'watch-' . $row->id) ?: ('watch-' . $row->id),
                'name' => $row->name ?: ('Watch ' . $row->id),
                'recommended_size' => '800x1000',
            ];

            $slug = $row->slug ?: $payload['slug'];
            $base = $slug;
            $i = 1;
            while (in_array($slug, $usedSlugs, true)) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $usedSlugs[] = $slug;

            $update = [
                'slug' => $slug,
                'recommended_size' => $row->recommended_size ?? $payload['recommended_size'],
                'sort_order' => $row->sort_order > 0 ? $row->sort_order : $position,
            ];

            if (empty($row->name) || in_array(strtoupper(trim((string) $row->name)), ['DSWDSWD', 'CEWAC'], true)) {
                $update['name'] = $payload['name'];
            }

            DB::table('watches')->where('id', $row->id)->update($update);
        }
    }

    public function down(): void
    {
        Schema::table('watches', function (Blueprint $table) {
            if (Schema::hasColumn('watches', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('watches', 'recommended_size')) {
                $table->dropColumn('recommended_size');
            }
        });
    }
};
