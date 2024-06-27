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
        Schema::create('food_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->index();
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreignId('television_id')->index();
            $table->foreign('television_id')->references('id')->on('televisions');
            $table->boolean('is_accepted')->nullable();
            $table->float('total');
            $table->string('payment_method');
            $table->string('order_id')->nullable();
            $table->string('qr_code')->nullable();
            $table->datetime('qr_code_expire_time')->nullable();
            $table->boolean('is_accepted')->nullable();
            $table->boolean('is_notified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_service_requests');
    }
};
