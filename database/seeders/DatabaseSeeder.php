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

        /** @var [Entreprise] $entreprises */
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

        /** @var User $user */
        foreach ($users as $user) {
            $passager = Passager::factory()->for($user)->create();

            if ($user->email == 'test1@test.com') {
                $user->assignRole('super admin');
            } else {
                $user->assignRole('user');
            }

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
}
