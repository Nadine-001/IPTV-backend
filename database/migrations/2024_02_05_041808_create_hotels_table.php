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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('class');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->string('greeting')->nullable();
            $table->string('about')->nullable();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->string('city');
            $table->string('phone')->nullable();
            $table->string('longitude')->nullable();
            $table->string('langitude')->nullable();
            $table->string('logo');
            $table->string('order_food_intro')->nullable();
            $table->string('qr_code_payment')->nullable();
            $table->string('qr_code_wifi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
