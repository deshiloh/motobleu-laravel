<?php

namespace Database\Seeders;

use App\Models\AdresseEntreprise;
use App\Models\AdresseReservation;
use App\Models\Carousel;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Localisation;
use App\Models\Page;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\User;
use App\Observers\ReservationObserver;
use Carbon\Carbon;
use Faker\Factory;
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
                ->for(Facture::factory()->create())
                ->for($passager)
                ->create()
            ;
        }

        Pilote::factory()->count(30)->create();

        Localisation::factory()->count(30)->create();

        Page::create([
            'title' => [
                'fr' => 'Politique Coookies',
                'en' => 'Cookies Policy'
            ],
            'content' => [
                'fr' => Factory::create('fr')->paragraph,
                'en' => Factory::create('en')->paragraph
            ],
            'slug' => [
                'fr' => \Str::slug('Politique Coookies'),
                'en' => \Str::slug('Cookies Policy')
            ]
        ]);

        Page::create([
            'title' => [
                'fr' => 'Mentions légales',
                'en' => 'Legals Mentions'
            ],
            'content' => [
                'fr' => Factory::create('fr')->paragraph,
                'en' => Factory::create('en')->paragraph
            ],
            'slug' => [
                'fr' => \Str::slug('Mentions légales'),
                'en' => \Str::slug('Legals Mentions')
            ]
        ]);

        if (App::environment(['test'])) {
            Carousel::factory()->count(20)->create();
        }
    }
}
