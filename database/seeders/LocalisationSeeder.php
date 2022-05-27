<?php

namespace Database\Seeders;

use App\Models\Localisation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Localisation::factory()->count(30)->create();
        Localisation::factory()->count(30)->nonActif()->create();
    }
}
