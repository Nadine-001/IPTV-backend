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
        Schema::create('room_service_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_service_request_id')->index();
            $table->foreign('room_service_request_id')->references('id')->on('room_service_requests');
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
        Schema::dropIfExists('room_service_request_details');
    }
};
