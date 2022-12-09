<?php

namespace App\Console\Commands;

use App\Models\AdresseReservation;
use App\Models\CostCenter;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Console\Command;
use MeiliSearch\Client;
use Mockery\Generator\StringManipulation\Pass\Pass;

class MeiliSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add configurations needed with melisearch';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        $this->info('Importations de la base de données et configuration');

        Entreprise::removeAllFromSearch();
        Entreprise::makeAllSearchable();
        $client->index('entreprises')->updateSortableAttributes([
            'nom',
            'is_actif'
        ]);

        Passager::removeAllFromSearch();
        Passager::makeAllSearchable();
        $client->index('passagers')->updateSortableAttributes([
            'nom',
            'secretaire'
        ]);

        Reservation::removeAllFromSearch();
        Reservation::makeAllSearchable();
        $client->index('reservations')->updateSortableAttributes([
            'id',
            'pickup_date',
            'localisation_from',
            'localisation_to',
            'entreprise',
        ]);
        $client->index('reservations')->updateFilterableAttributes([
            'is_cancel',
            'is_confirmed'
        ]);

        Pilote::removeAllFromSearch();
        Pilote::makeAllSearchable();
        $client->index('pilotes')->updateSortableAttributes([
            'nom',
            'prenom',
            'email',
            'adresse'
        ]);

        CostCenter::removeAllFromSearch();
        CostCenter::makeAllSearchable();
        $client->index('cost_centers')->updateSortableAttributes([
            'nom',
        ]);

        TypeFacturation::removeAllFromSearch();
        TypeFacturation::makeAllSearchable();
        $client->index('type_facturations')->updateSortableAttributes([
            'nom',
        ]);

        Localisation::removeAllFromSearch();
        Localisation::makeAllSearchable();
        $client->index('localisations')->updateSortableAttributes([
            'nom',
        ]);

        User::removeAllFromSearch();
        User::makeAllSearchable();

        AdresseReservation::removeAllFromSearch();
        AdresseReservation::makeAllSearchable();

        return 0;
    }
}
