<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniteVaccinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unite_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->string('nomUnite');
            $table->string('adresse');
            $table->string('categorie');
            $table->string('capacite');
            $table->string('uniteParent');
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
        Schema::dropIfExists('unite_vaccinations');
    }
}
