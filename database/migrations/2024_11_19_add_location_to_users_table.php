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
        Schema::table('users', function (Blueprint $table) {
            $table->string('current_street')->nullable()->after('email');
            $table->string('current_city')->nullable()->after('current_street');
            $table->string('current_province')->nullable()->after('current_city');
            $table->decimal('current_latitude', 10, 8)->nullable()->after('current_province');
            $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_street', 'current_city', 'current_province', 'current_latitude', 'current_longitude']);
        });
    }
};