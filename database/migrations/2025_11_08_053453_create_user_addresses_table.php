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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('phone_number');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('village_id');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('province_id')
                  ->references('id')
                  ->on('indonesia_provinces')
                  ->onDelete('cascade');

            $table->foreign('city_id')
                  ->references('id')
                  ->on('indonesia_cities')
                  ->onDelete('cascade');

            $table->foreign('district_id')
                  ->references('id')
                  ->on('indonesia_districts')
                  ->onDelete('cascade');

            $table->foreign('village_id')
                  ->references('id')
                  ->on('indonesia_villages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
}; 