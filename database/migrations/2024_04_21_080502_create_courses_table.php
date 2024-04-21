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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration')->nullable();
            $table->date('begin');
            $table->date('end');
            $table->boolean('valid');
            $table->unsignedBigInteger('trainer_id');
            $table->foreign('trainer_id')->references('id')
                ->on('trainers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')
                ->on('services')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
