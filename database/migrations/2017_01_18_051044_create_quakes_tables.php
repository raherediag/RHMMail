<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuakesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quakes', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('lng', 7, 3);
            $table->decimal('lat', 7, 3);
            $table->decimal('depth', 7, 3);
            $table->decimal('mag', 7, 3);
            $table->string('from');
            $table->text('description');
            $table->dateTime('date'); 
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
        Schema::drop('quakes');
    }
}
