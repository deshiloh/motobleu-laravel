<?php

namespace Database\Seeders;

use App\Enum\ReservationStatus;
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
use Exception;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->permissionsAndRolesSetting();

        $this->importOldData();

//        /** @var [Entreprise] $entreprises */
//        $entreprises = Entreprise::factory()
//            ->count(2)
//            ->has(AdresseEntreprise::factory()->facturation())
//            ->has(AdresseEntreprise::factory()->physique())
//        ;
//
//        $users = User::factory()
//            ->has($entreprises)
//            ->has(AdresseReservation::factory()->count(5))
//            ->count(10)
//            ->create();
//
//        /** @var User $user */
//        foreach ($users as $user) {
//            $passager = Passager::factory()->for($user)->create();
//
//            if ($user->email == 'test1@test.com') {
//                $user->assignRole('super admin');
//            } else {
//                $user->assignRole('user');
//            }
//
//            Reservation::factory([
//                'pickup_date' => Carbon::now(),
//                'statut' => ReservationStatus::Confirmed,
//                'entreprise_id' => $user->entreprises()->first()->id,
//            ])
//                ->for(Facture::factory(['montant_ht' => 0])->create())
//                ->for($passager)
//                ->for(Pilote::factory()->create())
//                ->create()
//            ;
//
//            Reservation::factory([
//                'statut' => ReservationStatus::Created,
//                'entreprise_id' => $user->entreprises()->first()->id,
//            ])
//                ->for($passager)
//                ->create();
//        }
//
//        Pilote::factory()->count(30)->create();
//
//        Localisation::factory()->count(30)->create();
//
//        Page::create([
//            'title' => [
//                'fr' => 'Politique Coookies',
//                'en' => 'Cookies Policy'
//            ],
//            'content' => [
//                'fr' => Factory::create('fr')->paragraph,
//                'en' => Factory::create('en')->paragraph
//            ],
//            'slug' => [
//                'fr' => \Str::slug('Politique Coookies'),
//                'en' => \Str::slug('Cookies Policy')
//            ]
//        ]);
//
//        Page::create([
//            'title' => [
//                'fr' => 'Mentions légales',
//                'en' => 'Legals Mentions'
//            ],
//            'content' => [
//                'fr' => Factory::create('fr')->paragraph,
//                'en' => Factory::create('en')->paragraph
//            ],
//            'slug' => [
//                'fr' => \Str::slug('Mentions légales'),
//                'en' => \Str::slug('Legals Mentions')
//            ]
//        ]);

        if (App::environment(['test'])) {
            Carousel::factory()->count(20)->create();
        }
    }

    private function permissionsAndRolesSetting()
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
    }

    private function importOldData()
    {
        $prodConnecion = \Illuminate\Support\Facades\DB::connection('prod');

        // Création des entreprises
        $prodConnecion->table('entreprise')->orderBy('id', 'asc')->chunk(100, function ($entreprises) {
            DB::table('entreprises')->insertOrIgnore((array)json_decode(json_encode($entreprises->toArray()), true));
        });

        // Création des utilisateurs
        $prodConnecion->table('user')->orderBy('id', 'asc')->select(['id', 'nom', 'email', 'prenom', 'entreprise_id'])->chunk(100, function ($users) {
            $users->map(function($user) {
                try {
                    $idInsert = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
                        'id' => $user->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'password' => \Illuminate\Support\Facades\Hash::make('test'),
                        'email' => $user->email
                    ]);

                    // Role
                    if ($user->email == 'm.alvarez.iglisias@gmail.com') {
                        /** @var User $user */
                        $user = User::find($idInsert);
                        $user->assignRole('super admin');
                    }

                    // Liaison entre utilisateur et entreprise
                    \Illuminate\Support\Facades\DB::table('entreprise_user')->insert([
                        'user_id' => $user->id,
                        'entreprise_id' => $user->entreprise_id
                    ]);
                } catch (Exception $exception) {
                    ray()->exception($exception);
                }
            });
        });

        // Création des pilotes
        $prodConnecion->table('pilote')->orderBy('id', 'asc')->chunk(100, function ($pilotes) {
            DB::table('pilotes')->insertOrIgnore((array)json_decode(json_encode($pilotes->toArray()), true));
        });

        // Cost Center
        $prodConnecion->table('cost_center')->orderBy('id', 'asc')->chunk(100, function ($costs) {
            $costs = $costs->map(fn($cost) => [
                'id' => $cost->id,
                'nom' => $cost->title,
                'is_actif' => $cost->actif,
                'created_at' => $cost->created_at
            ]);
            DB::table('cost_centers')->insertOrIgnore((array)json_decode(json_encode($costs->toArray()), true));
        });

        // Type Facturation
        $prodConnecion->table('facturation')->orderBy('id', 'asc')->chunk(100, function ($facturations) {
            $facturations = $facturations->map(fn($facturation) => [
                'id' => $facturation->id,
                'nom' => $facturation->titre,
            ]);
            DB::table('type_facturations')->insertOrIgnore((array)json_decode(json_encode($facturations->toArray()), true));
        });

        // Création des passagers
//        $prodConnecion->table('passager')->orderBy('id', 'asc')->chunk(100, function ($passagers) {
//            $passagers->map(fn($passager) => [
//                'id' => $passager->id,
//                'user_id' => $passager->user_id,
//                'nom' => $passager->nom,
//                'telephone' => $passager->tel_office,
//                'portable' => $passager->tel_port,
//                'email' => $passager->email,
//                'cost_center_id' => $passager->cost_center_id,
//                'type_facturation_id' => $passager->facturation_item_id
//            ]);
//        });
    }
}
