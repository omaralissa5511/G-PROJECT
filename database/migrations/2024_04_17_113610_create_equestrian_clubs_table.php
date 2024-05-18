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
        Schema::create('equestrian_clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('description');
            $table->string('profile');
            $table->string('day')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->string('license')->nullable();
            $table->string('website');
            $table->decimal('lat');
            $table->decimal('long');
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
        Schema::dropIfExists('equestrian_clubs');
    }
    protected $casts = [
        'days' => 'array',
    ];
};
