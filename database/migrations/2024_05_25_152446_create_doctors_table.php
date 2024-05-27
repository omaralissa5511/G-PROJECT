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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('lastName')->nullable();
            $table->date('birth');
            $table->string('gender');
            $table->string('image');
            $table->text('description');
            $table->integer('experience')->nullable();
            $table->text('specialties')->nullable();


            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                  ->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('health_care_id');
            $table->foreign('health_care_id')->references('id')
                  ->on('health_cares')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
