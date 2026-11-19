<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access - can manage everything',
                'permissions' => json_encode(['all']),
                'is_system' => true,
                'icon' => 'ti-shield',
                'color' => 'danger',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'web_designer',
                'display_name' => 'Web Designer',
                'description' => 'Manages CMS, banners, sliders, and blogs',
                'permissions' => json_encode(['dashboard', 'cms', 'sliders', 'blogs', 'banners']),
                'is_system' => true,
                'icon' => 'ti-palette',
                'color' => 'info',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order_manager',
                'display_name' => 'Order Manager',
                'description' => 'Manages orders, transactions, and refunds',
                'permissions' => json_encode(['dashboard', 'orders', 'transactions', 'refunds', 'reports']),
                'is_system' => true,
                'icon' => 'ti-shopping-cart',
                'color' => 'warning',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Oversees operations and manages reports',
                'permissions' => json_encode(['dashboard', 'orders', 'transactions', 'reports']),
                'is_system' => true,
                'icon' => 'ti-user-check',
                'color' => 'success',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Regular customer - frontend access only',
                'permissions' => json_encode([]),
                'is_system' => true,
                'icon' => 'ti-user',
                'color' => 'secondary',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};