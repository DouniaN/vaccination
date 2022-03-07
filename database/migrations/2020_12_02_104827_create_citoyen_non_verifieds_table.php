<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitoyenNonVerifiedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citoyen_non_verifieds', function (Blueprint $table) {
            $table->id();
            $table->string('cin')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nomAr');
            $table->string('prenomAr');
            $table->date('dateNaissance');
            $table->string('sexe');
            $table->string('adresse');
            $table->string('tel');
            $table->string('email')->nullable();
            $table->string('profession')->nullable();
            $table->unsignedBigInteger('quartier_id');
            $table->string('lieu_travail');
            $table->string('maladie_chronique');
            $table->string('maladie_cardiaque');
            $table->string('maladie_foie');
            $table->string('hypertention');
            $table->string('cancer');
            $table->string('enceinte');
            $table->string('sufisance_renal');
            $table->string('maladie_resperatoire');
            $table->string('sys_uminitaire');

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
        Schema::dropIfExists('citoyen_non_verifieds');
    }
}
