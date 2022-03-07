<?php

use App\Province;
use Illuminate\Database\Seeder;

class provinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Province::class,250)->create();
    }
}
