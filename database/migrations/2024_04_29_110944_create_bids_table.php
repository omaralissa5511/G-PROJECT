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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->decimal('offeredPrice', 17, 2);
            $table->unsignedBigInteger('profile_id');
            $table->foreign('profile_id')->references('id')->on
            ('profiles')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('bids');
    }
};
