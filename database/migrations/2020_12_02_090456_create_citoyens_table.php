<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitoyensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citoyens', function (Blueprint $table) {
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
           
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('citoyens');
    }
}
