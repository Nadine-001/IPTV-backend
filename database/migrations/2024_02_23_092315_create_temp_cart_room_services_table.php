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
        Schema::create('temp_cart_room_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->index();
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreignId('television_id')->index();
            $table->foreign('television_id')->references('id')->on('televisions');
            $table->foreignId('room_service_id')->index();
            $table->foreign('room_service_id')->references('id')->on('room_services');
            $table->integer('qty');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_cart_room_services');
    }
};
