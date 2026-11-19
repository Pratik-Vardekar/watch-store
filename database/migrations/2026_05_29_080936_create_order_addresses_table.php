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
        Schema::create('order_addresses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('type', ['billing', 'shipping']);

            $table->string('name');

            $table->string('phone');

            $table->string('alternate_phone')->nullable();

            $table->string('company_name')->nullable();

            $table->string('gstin')->nullable();

            $table->text('address');

            $table->string('landmark')->nullable();

            $table->string('city');

            $table->string('state');

            $table->string('country')->default('India');

            $table->string('pincode');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};