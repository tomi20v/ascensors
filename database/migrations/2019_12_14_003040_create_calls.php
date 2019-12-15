<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalls extends Migration
{

    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('fromLevel');
            $table->integer('toLevel');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calls');
    }
}
