<?php

namespace Database\Seeders;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 4; $i++) {
            $user = User::factory([
                'email' => "test" . $i . "@test.com",
                'password' => Hash::make('test'),
            ])->create();

            /*$entreprise = Entreprise::factory()->create();
            $adresseEntreprise = AdresseEntreprise::factory()->make();

            $user->entreprise()->save($entreprise);

            $entreprise->adresseEntreprises()->save($adresseEntreprise);*/
        }
    }
}
