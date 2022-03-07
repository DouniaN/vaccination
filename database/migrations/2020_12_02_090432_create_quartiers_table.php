<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuartiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->string('nomQuartier');
            $table->unsignedBigInteger('commandement_id');
            $table->foreign('commandement_id')->references('id')->on('commandements');
            $table->unsignedBigInteger('commun_id');
            $table->foreign('commun_id')->references('id')->on('communs');
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
        Schema::dropIfExists('quartiers');
    }
}
