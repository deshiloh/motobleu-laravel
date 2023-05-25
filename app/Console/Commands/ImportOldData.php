<?php

namespace App\Console\Commands;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
use App\Models\Facture;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Command\Command as CommandAlias;
use function PHPUnit\Framework\matches;

class ImportOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $reservationPermissions = [
            'see reservation',
            'create reservation',
            'edit reservation',
            'delete reservation'
        ];

        $addressReservationPermissions = [
            'see address reservation',
            'create address reservation',
            'edit address reservation',
            'delete address reservation'
        ];

        $costCenterPermissions = [
            'see cost center',
            'create cost center',
            'edit cost center',
            'delete cost center'
        ];

        $facturePermissions = [
            'see facture',
            'create facture',
            'edit facture',
            'delete facture'
        ];

        $passengerPermissions = [
            'see passenger',
            'create passenger',
            'edit passenger',
            'delete passenger'
        ];

        $userPermissions = [
            'see user',
            'create user',
            'edit user',
            'delete user'
        ];

        $arrayOfPermissionNames = array_merge(
            $reservationPermissions,
            $addressReservationPermissions,
            $costCenterPermissions,
            $facturePermissions,
            $passengerPermissions,
            $userPermissions
        );

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());

        $arrayOfRoles = ['user', 'admin', 'super admin'];
        $roles = collect($arrayOfRoles)->map(function ($role) {
            return ['name' => $role, 'guard_name' => 'web'];
        });

        Role::insert($roles->toArray());

        $roleHasPermission = [
            'user' => array_merge(
                $reservationPermissions,
                $addressReservationPermissions,
                $passengerPermissions
            ),
            'admin' => array_merge(
                $reservationPermissions,
                $addressReservationPermissions,
                $costCenterPermissions,
                $facturePermissions,
                $userPermissions,
                $passengerPermissions
            )
        ];

        foreach ($roleHasPermission as $role => $permissions) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissions)
                        ->map(function ($permissionName) use ($role) {
                            $permission = Permission::whereName($permissionName)->first();
                            return ['role_id' => $role->id, 'permission_id' => $permission->id];
                        })
                        ->toArray()
                );
        }

        $prodConnecion = DB::connection('prod');

        // Création des entreprises
        $prodConnecion->table('entreprise')->orderBy('id')->chunk(100, function ($entreprises) {
            DB::table('entreprises')->insertOrIgnore((array)json_decode(json_encode($entreprises->toArray()), true));
        });

        $prodConnecion->table('adresse_entreprise')->orderBy('id')->chunk(200, function ($addressEntreprise) {
            $addressEntreprise->map(fn($item) => $item->type = $item->type == 'facturation' ? AdresseEntrepriseTypeEnum::FACTURATION->value : AdresseEntrepriseTypeEnum::PHYSIQUE->value);
            DB::table('adresse_entreprises')->insertOrIgnore((array)json_decode(json_encode($addressEntreprise->toArray()), true));
        });

        // Création des utilisateurs
        $prodConnecion->table('user')->orderBy('id')->select(['id', 'nom', 'email', 'prenom', 'roles'])->chunk(100, function ($users) {
            $users->map(function($user) {
                try {
                    $idInsert = DB::table('users')->insertGetId([
                        'id' => $user->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'password' => Hash::make('test'),
                        'email' => $user->email,
                        'is_admin' => str_contains($user->roles, 'ROLE_ARDIAN_WATCHER')
                    ]);

                    /** @var User $user */
                    $user = User::find($idInsert);

                    switch ($user->email) {
                        case 'm.alvarez.iglisias@gmail.com':
                        case 'contact@motobleu-paris.com':
                        case 'contact@apc66.com':
                            $user->assignRole('super admin');
                            break;
                    }

                    if ($user->is_admin) {
                        $user->assignRole('admin');
                    }

                } catch (Exception $exception) {
                    $this->error($exception->getMessage());
                }
            });
        });

        $prodConnecion->table('entreprise_user')->orderBy('user_id')->chunk(200, function ($entrepriseUser) {
            DB::table('entreprise_user')->insertOrIgnore((array)json_decode(json_encode($entrepriseUser->toArray()), true));
        });

        $userNotAdminArdian = User::whereHas('entreprises', function (Builder $query) {
            return $query->where('nom', 'Ardian France');
        })
            ->where('is_admin', false)->get();

        $userNotAdminArdian->map(function($user) {
            $user->assignRole('user');
        });

        $users = User::whereDoesntHave('entreprises', function (Builder $query) {
            return $query->where('nom', 'Ardian France');
        })
            ->where('is_admin', false)
            ->get();

        $users->map(function($user) {
            $user->assignRole('admin');
        });

        // Création des pilotes
        $prodConnecion->table('pilote')->orderBy('id')->chunk(100, function ($pilotes) {
            DB::table('pilotes')->insertOrIgnore((array)json_decode(json_encode($pilotes->toArray()), true));
        });

        // Cost Center
        $prodConnecion->table('cost_center')->orderBy('id')->chunk(100, function ($costs) {
            $costs = $costs->map(fn($cost) => [
                'id' => $cost->id,
                'nom' => $cost->title,
                'is_actif' => $cost->actif,
                'created_at' => $cost->created_at
            ]);
            DB::table('cost_centers')->insertOrIgnore((array)json_decode(json_encode($costs->toArray()), true));
        });

        // Type Facturation
        $prodConnecion->table('facturation')->orderBy('id')->chunk(100, function ($facturations) {
            $facturations = $facturations->map(fn($facturation) => [
                'id' => $facturation->id,
                'nom' => $facturation->titre,
            ]);
            DB::table('type_facturations')->insertOrIgnore((array)json_decode(json_encode($facturations->toArray()), true));
        });

        // Création des passagers
        $prodConnecion->table('passager')->orderBy('id')->chunk(200, function ($passagers) {
            $passagers = $passagers->map(fn($passager) => [
                'id' => $passager->id,
                'user_id' => $passager->user_id,
                'nom' => $passager->nom,
                'telephone' => $passager->tel_office,
                'portable' => $passager->tel_port,
                'email' => $passager->email,
                'cost_center_id' => $passager->cost_center_id,
                'type_facturation_id' => $passager->facturation_item_id,
                'is_actif' => $passager->actif
            ]);
            DB::table('passagers')->insertOrIgnore((array)json_decode(json_encode($passagers->toArray()), true));
        });

        $prodConnecion->table('localisation')->orderBy('id')->chunk(200, function ($localisations) {
            $localisations = $localisations->map(fn ($item) => [
                'id' => $item->id,
                'nom' => $item->nom,
                'adresse' => $item->adresse,
                'adresse_complement' => $item->adresse_bis,
                'code_postal' => $item->code_postal,
                'ville' => $item->ville,
                'is_actif' => $item->is_actif,
                'telephone' => $item->telephone
            ]);
            DB::table('localisations')->insertOrIgnore((array)json_decode(json_encode($localisations->toArray()), true));
        });

        $prodConnecion->table('adresse_reservation')->orderBy('id')->chunk(200, function ($addresses) {
            $addresses = $addresses->map(fn($item) => [
                'id' => $item->id,
                'adresse' => $item->adresse,
                'adresse_complement' => $item->adresse_complement,
                'code_postal' => $item->code_postal,
                'ville' => $item->ville,
                'is_actif' => $item->actif,
                'is_deleted' => $item->is_deleted,
                'user_id' => $item->user_id
            ]);

            DB::table('adresse_reservations')->insertOrIgnore((array)json_decode(json_encode($addresses->toArray()), true));
        });

        $prodConnecion->table('facture')->orderBy('id')->chunk(200, function ($factures) {
            $factures = $factures->map(fn($item) => [
                'id' => $item->id,
                'statut' => BillStatut::COMPLETED->value,
                'reference' => $item->reference,
                'montant_ht' => $item->montant_ttc - $item->montant_tva,
                'tva' => 10,
                'adresse_client' => $item->adresse_client,
                'adresse_facturation' => $item->adresse_facturation,
                'information' => $item->information,
                'month' => $item->month,
                'year' => $item->year,
                'is_acquitte' => $item->is_acquitte,
                'created_at' => $item->created_at
            ]);

            DB::table('factures')->insertOrIgnore((array)json_decode(json_encode($factures->toArray()), true));
        });

        $prodConnecion->table('reservation')->orderBy('id')->chunk(100, function ($reservations) {
            $reservations = $reservations->map(fn($item) => [
                'id' => $item->id,
                'statut' => $this->getEtatValue($item),
                'commande' => $item->num_commande,
                'reference' => $item->reference,
                'pickup_origin' => $item->pick_up_origin,
                'drop_off_origin' => $item->dropp_of_origin,
                'comment' => $item->comment,
                'encaisse_pilote' => $item->encaisse_pilote,
                'encompte_pilote' => $item->encompte_pilote,
                'tarif' => $item->prix,
                'majoration' => $item->majoration,
                'complement' => $item->complement,
                'comment_facture' => $item->comment_facturation,
                'send_to_passager' => $item->send_to_passager,
                'has_back' => (int)$item->retour_id > 0,
                'calendar_passager_invitation' => (int)$item->send_to_passager_invitation,
                'pickup_date' => $item->pickup_date,
                'localisation_from_id' => $item->pickup_localisation_id,
                'localisation_to_id' => $item->drop_off_localisation_id,
                'adresse_reservation_from_id' => $item->pick_up_address_id,
                'adresse_reservation_to_id' => $item->drop_off_adress_id,
                'passager_id' => $item->passager_id,
                'pilote_id' => $item->pilote_id,
                'reservation_id' => $item->retour_id,
                'entreprise_id' => $item->entreprise_id,
                'facture_id' => $item->facture_id,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('reservations')->insert((array)json_decode(json_encode($reservations->toArray()), true));
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });

        // Check réservations importées
        $nbCreatedReservation = Reservation::where('statut', ReservationStatus::Created)->count();
        $nbCreatedReservationProd = $prodConnecion->table('reservation')
            ->where('is_factured', 0)
            ->where('is_canceled', 0)
            ->where('cancel_pay', 0)
            ->where('is_confirmed', 0)
            ->count();

        if ($nbCreatedReservation === $nbCreatedReservationProd) {
            $this->info("Réservations en statut créée correctement importées");
        } else {
            $this->error(sprintf(
                "Le nombre de réservations en statut créée ne correspond pas à celui de la PROD %s contre %s en prod",
                $nbCreatedReservation,
                $nbCreatedReservationProd
            ));
        }

        $nbBilledReservation = Reservation::where('statut', ReservationStatus::Billed)->count();
        $nbBilledReservationProd = $prodConnecion->table('reservation')
            ->where('is_factured', 1)
            ->count();

        if ($nbBilledReservation === $nbBilledReservationProd) {
            $this->info("Réservations en statut facturée correctement importées");
        } else {
            $this->error(sprintf(
                "Le nombre de réservations en statut facturée ne correspond pas à celui de la PROD %s contre %s en prod",
                $nbBilledReservation,
                $nbBilledReservationProd
            ));
        }

        // Check des données de facturations
        $facturesImported = Facture::all();

        /** @var Facture $facture */
        foreach ($facturesImported as $facture) {
            $nbReservationFactureExported = $facture->reservations()->count();
            $nbReservationFactureProd = $prodConnecion->table('reservation')
                ->where('facture_id', $facture->id)->count();

            if ($nbReservationFactureProd !== $nbReservationFactureExported) {
                $this->error(
                    sprintf("La facture %s avec ID %s n'est pas bonne.", $facture->reference, $facture->id)
                );
            }
        }

        $this->info("Factures correctement importées");

        return CommandAlias::SUCCESS;
    }

    private function getEtatValue($reservation): int
    {
        if ($reservation->is_factured === 1) {
            return ReservationStatus::Billed->value;
        }

        if ($reservation->is_canceled === 1) {
            return ReservationStatus::Canceled->value;
        }

        if ($reservation->cancel_pay === 1) {
            return ReservationStatus::CanceledToPay->value;
        }

        if ($reservation->is_confirmed === 1 && is_null($reservation->facture_id)) {
            return ReservationStatus::Confirmed->value;
        }

        return ReservationStatus::Created->value;
    }
}
