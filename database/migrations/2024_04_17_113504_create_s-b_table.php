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
        Schema::create('sellerbuyers' , function (Blueprint $table){

            $table->id();
            $table->string('FName');
            $table->string('lName')->nullable();
            $table->date('birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('address');
            $table->string('image')->nullable();
            $table->string('license')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
