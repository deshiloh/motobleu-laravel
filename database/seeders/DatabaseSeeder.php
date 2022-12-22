<?php

namespace Database\Seeders;

use App\Models\AdresseEntreprise;
use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\User;
use App\Observers\ReservationObserver;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

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
        $entreprises = Entreprise::factory()
            ->count(2)
            ->has(AdresseEntreprise::factory()->facturation())
            ->has(AdresseEntreprise::factory()->physique())
        ;

        $users = User::factory()
            ->has($entreprises)
            ->has(AdresseReservation::factory()->count(5))
            ->count(10)
            ->create();

        foreach ($users as $user) {
            $passager = Passager::factory()->for($user)->create();

            Reservation::factory([
                'pickup_date' => Carbon::now(),
                'is_confirmed' => true,
                'entreprise_id' => 1,
            ])
                ->for($passager)
                ->create()
            ;
        }

        Localisation::factory()->count(30)->create();
    }
}
