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
        Schema::create('food_service_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_service_request_id')->index();
            $table->foreign('food_service_request_id')->references('id')->on('food_service_requests');
            $table->foreignId('menu_id')->index();
            $table->foreign('menu_id')->references('id')->on('menus');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_service_request_details');
    }
};
