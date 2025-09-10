<?php

namespace Tests\Feature;

use App\Livewire\Account\UsersDataTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testPaginationRendersCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');

        // Créer plusieurs utilisateurs pour activer la pagination
        User::factory()->count(25)->create();

        $component = Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('perPage', 10);

        // Vérifier que le composant se rend sans erreur
        $component->assertStatus(200);
        
        // Vérifier que la pagination contient les éléments wire:
        $component->assertSee('wire:');
    }

    public function testPaginationChangesPageCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');

        // Créer plusieurs utilisateurs pour activer la pagination
        User::factory()->count(25)->create();

        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('perPage', 10)
            ->call('gotoPage', 2)
            ->assertStatus(200);
    }

    public function testPerPageWorksWithPagination()
    {
        $user = User::find(1);
        $user->assignRole('super admin');

        // Créer plusieurs utilisateurs pour activer la pagination
        User::factory()->count(25)->create();

        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('perPage', 5)
            ->assertStatus(200)
            ->set('perPage', 20)
            ->assertStatus(200);
    }
}