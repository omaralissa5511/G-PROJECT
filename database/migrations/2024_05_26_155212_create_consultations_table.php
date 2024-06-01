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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('health_care_id');
            $table->text('content');
            $table->timestamp('sent_at');
            $table->text('reply_content')->nullable();
            $table->timestamp('reply_sent_at')->nullable();
            $table->string('name');
            $table->string('color');
            $table->text('symptoms');
            $table->string('age');
            $table->string('gender');
            $table->foreign('profile_id')->references('id')->on('profiles')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('health_care_id')->references('id')->on('health_cares');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
