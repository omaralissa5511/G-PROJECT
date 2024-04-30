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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horse_id');
            $table->foreign('horse_id')->references('id')->on
            ('horses')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('auction_id');
            $table->foreign('auction_id')->references('id')->on
            ('auctions')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_aperiods');
    }
};
