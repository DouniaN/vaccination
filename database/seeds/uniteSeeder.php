<?php

use App\Unite_vaccination;
use Illuminate\Database\Seeder;

class uniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Unite_vaccination::class,150)->create();
    }
}
