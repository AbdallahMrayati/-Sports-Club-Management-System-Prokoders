<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sport_day', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('day_id');
            $table->timestamps();

            $table->foreign('sport_id')->references("id")->on('sports')->onDelete('cascade');
            $table->foreign('day_id')->references("id")->on('days')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sport_day');
    }
};