<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandements', function (Blueprint $table) {
            $table->id();
            $table->string('nomCommandement');
            $table->unsignedBigInteger('pachalik_id');
            $table->foreign('pachalik_id')->references('id')->on('pachaliks');
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
        Schema::dropIfExists('commandements');
    }
}
