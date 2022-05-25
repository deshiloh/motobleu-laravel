<?php

namespace Database\Seeders;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class EntrepriseSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*Entreprise::factory()
            ->count(40)
            ->create()
            ->each(function (Entreprise $entreprise) {
                $adresse = AdresseEntreprise::factory()->make();
                $entreprise->adresseEntreprises()->save($adresse);
            })
        ;*/
    }
}
