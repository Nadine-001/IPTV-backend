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
        Schema::create('hotel_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->index();
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->string('name');
            $table->string('description');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_facilities');
    }
};
