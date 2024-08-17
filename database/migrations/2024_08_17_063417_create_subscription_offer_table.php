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
        Schema::create('subscription_offer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('offer_id');
            $table->timestamps();

            $table->foreign('subscription_id')->references("id")->on('subscriptions')->onDelete('cascade');
            $table->foreign('offer_id')->references("id")->on('offers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_offer');
    }
};