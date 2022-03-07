<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Unite_vaccination;
use Faker\Generator as Faker;

$factory->define(Unite_vaccination::class, function (Faker $faker) {
    return [
        'nomUnite'=>$faker->company,
        'adresse'=>$faker->address,
        'categorie'=>$faker->companySuffix,
        'capacite'=>random_int(50,200),
        'uniteParent'=>'',
    ];
});
