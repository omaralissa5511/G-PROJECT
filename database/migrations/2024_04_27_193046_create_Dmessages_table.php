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
        Schema::create('d_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('doctor_id');
            $table->integer('user');
            $table->integer('doctor')->nullable();
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
        Schema::dropIfExists('D_messages');
    }
};
