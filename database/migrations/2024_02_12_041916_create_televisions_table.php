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
        Schema::create('televisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->index();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->string('mac_address');
            $table->string('guest_name')->nullable();
            $table->string('guest_gender')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('televisions');
    }
};
