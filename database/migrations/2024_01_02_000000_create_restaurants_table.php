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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone', 20);
            $table->integer('min_price')->default(0);
            $table->integer('max_price')->default(0);
            $table->text('description')->nullable();
            $table->string('opening_hours')->nullable(); // Contoh: "08:00 - 22:00"
            $table->string('opening_days')->nullable();  // Contoh: "Senin - Minggu"
            $table->decimal('rating', 3, 2)->default(0); // Contoh: 4.50
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('google_maps_link')->nullable();
            $table->string('image')->nullable(); // Path to image
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};