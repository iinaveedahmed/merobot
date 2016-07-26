<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('robot_id');
            $table->integer('x');
            $table->integer('y');
            $table->string('heading');
            $table->unsignedInteger('step');
            $table->foreign('robot_id')->references('id')->on('robots')->onDelete('cascade');
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
        Schema::drop('roads');
    }
}
