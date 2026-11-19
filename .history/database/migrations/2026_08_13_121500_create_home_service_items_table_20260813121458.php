<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_service_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('image_updated_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        $now = now();

        DB::table('home_service_items')->insert([
            [
                'title' => 'Battery',
                'description' => 'We keep original batteries brands Seiko and Renata',
                'image' => 'frontend/assets/images/serv_Battery.jpg',
                'image_updated_at' => $now,
                'sort_order' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Strap',
                'description' => 'We keep straps like genuine leather, metal, pvc & brand straps',
                'image' => 'frontend/assets/images/serv_Strap.jpg',
                'image_updated_at' => $now,
                'sort_order' => 2,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Glass',
                'description' => 'We can change any watch glass',
                'image' => 'frontend/assets/images/serv_Glass.jpg',
                'image_updated_at' => $now,
                'sort_order' => 3,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Mechanical',
                'description' => 'We have trained & specialized technician for mechanical watches',
                'image' => 'frontend/assets/images/serv_Mechanical.jpg',
                'image_updated_at' => $now,
                'sort_order' => 4,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_service_items');
    }
};
