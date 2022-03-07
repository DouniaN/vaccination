<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Province;
use Faker\Generator as Faker;

$factory->define(Province::class, function (Faker $faker) {
    return [
        'nomProvince'=>$faker->city,
        'region_id'=>1,
        'created_at'=>now(),
        'updated_at'=>now(),
    ];
});
