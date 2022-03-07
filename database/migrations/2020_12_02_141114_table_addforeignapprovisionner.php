<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAddforeignapprovisionner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appprovisionnements', function (Blueprint $table) {
            $table->unsignedBigInteger('vaccin_id');
            $table->foreign('vaccin_id')->references('id')->on('vaccins');
            $table->unsignedBigInteger('unite_vaccination_id');
            $table->foreign('unite_vaccination_id')->references('id')->on('unite_vaccinations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appprovisionnements');
    }
}
