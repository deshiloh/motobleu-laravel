<?php

namespace Database\Seeders;

use App\Models\AdresseEntreprise;
use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\User;
use App\Observers\ReservationObserver;
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
            ->count(10)
            ->has(AdresseEntreprise::factory())
            ->create();

        foreach ($entreprises as $entreprise) {
            $users = User::factory()
                ->count(4)
                ->has(AdresseReservation::factory())
                ->for($entreprise)
                ->create();

            foreach ($users as $user) {
                $passager = Passager::factory()->for($user)->create();
                if (App::environment(['testing'])) {
                    Reservation::factory()->for($passager)->create();
                }
                Reservation::factory([
                    'is_confirmed' => true
                ])->for($passager)->create();
            }
        }

        if (App::environment(['local', 'prod'])) {
            Localisation::factory()->count(30)->create();
        }

        Pilote::factory()->count(20)->create();

        if (App::environment(['local', 'prod'])) {
            Artisan::call('melisearch:setting');
        }
    }
}
