<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use App\Models\Localisation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeed::class,
            PiloteSeeder::class,
            LocalisationSeeder::class,
            CostCenterSeeder::class
        ]);
    }
}
