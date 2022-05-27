<?php

namespace Database\Seeders;

use App\Models\CostCenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CostCenter::factory()->count(20)->create();
        CostCenter::factory()->nonActif()->count(20)->create();
    }
}
