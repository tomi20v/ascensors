<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAscensors extends Migration
{

    public function up()
    {
        Schema::create('ascensors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('column');
            $table->integer('level')->default(0);
            $table->integer('traveled')->default(0);
            $table->integer('lastMinute')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ascensors');
    }
}
