<?php

namespace Database\Seeders;

use App\Models\Pilote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PiloteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pilote::factory()->count(20)->create();
    }
}
