<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 4; $i++) {
            User::create([
                'nom' => 'Test',
                'prenom' => 'test',
                'email' => "test" . $i . "@test.com",
                'password' => Hash::make('test'),
                'telephone' => '0788485425',
                'adresse' => 'toto',
                'adresse_bis' => 'toto',
                'code_postal' => '34000',
                'ville' => 'Montpellier',
                'is_admin_ardian' => false,
                'is_actif' => true
            ]);
        }
    }
}
