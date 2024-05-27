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
        Schema::create('consultation_details', function (Blueprint $table) {
            $table->id();
            $table->text('details');
            $table->date('date');
            $table->enum('type',['vaccination','treatment','medical']);
            $table->unsignedBigInteger('consultation_id');
            $table->foreign('consultation_id')->references('id')
                ->on('consultations')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_details');
    }
};
