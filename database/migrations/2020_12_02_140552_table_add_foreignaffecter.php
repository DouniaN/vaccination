<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAddForeignaffecter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affecters', function (Blueprint $table) {
            $table->unsignedBigInteger('citoyen_id');
            $table->foreign('citoyen_id')->references('id')->on('citoyens');
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
        Schema::dropIfExists('affecters');
    }
}
