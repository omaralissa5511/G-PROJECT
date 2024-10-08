<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
/**
* Run the migrations.
*
* @return void
*/
public function up()
{
Schema::create('notifications', function (Blueprint $table) {
$table->id();
$table->string('type');
$table->morphs('notifiable');
$table->string('title')->default('title');
$table->text('message');
$table->text('data');
$table->timestamp('read_at')->nullable();
$table->timestamps();
});
}

/**
* Reverse the migrations.
*
* @return void
*/
public function down()
{
Schema::dropIfExists('notifications');
}
}
