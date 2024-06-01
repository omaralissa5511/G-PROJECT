<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */ public function up(): void
    {
        Schema::create('trainers' , function (Blueprint $table){

            $table->id();
            $table->string('FName');
            $table->string('lName')->nullable();
            $table->date('birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('address');
            $table->string('days');
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->text('channelName')->default('hi');
            $table->string('license')->nullable();
            $table->string('images')->nullable();
            $table->string('image')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('certifications')->nullable();
            $table->integer('experience')->nullable();
            $table->text('specialties')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('club_id');
            $table->foreign('club_id')->references('id')
                ->on('equestrian_clubs')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
    protected $casts = [
        'days' => 'array',
        'images' => 'array'
    ];
};
