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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('trainer_id');
            $table->boolean('user');
            $table->integer('trainer')->nullable();
            $table->string('time');
            $table->text('content')->nullable();
            $table->string('role');
            $table->string('image')->nullable();
            $table->boolean('read')->default(0); // 0 => not read
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
