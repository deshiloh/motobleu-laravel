<?php

namespace Tests\Feature;

use App\Livewire\Account\UsersDataTable;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersDataTableSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testSearchByEntrepriseWorksCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');
        
        // Créer une entreprise spécifique pour le test
        $entreprise = Entreprise::factory()->create(['nom' => 'Entreprise Test Search']);
        
        // Créer un utilisateur et l'attacher à l'entreprise
        $testUser = User::factory()->create(['nom' => 'Test User']);
        $testUser->entreprises()->attach($entreprise->id);
        
        // Tester le composant Livewire avec le filtre entreprise
        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('selectedEntreprise', $entreprise->id)
            ->assertSee('Test User');
    }

    public function testSearchByNameWorksCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');
        
        // Créer un utilisateur avec un nom spécifique
        $testUser = User::factory()->create(['nom' => 'NomUnique123']);
        
        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('search', 'NomUnique123')
            ->assertSee('NomUnique123');
    }

    public function testClearFiltersWorksCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');
        
        $entreprise = Entreprise::find(1);
        
        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->set('search', 'test')
            ->set('selectedEntreprise', $entreprise->id)
            ->set('perPage', 100)
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('selectedEntreprise', null)
            ->assertSet('perPage', 20);
    }

    public function testPerPageChangesCorrectly()
    {
        $user = User::find(1);
        $user->assignRole('super admin');
        
        Livewire::actingAs($user)
            ->test(UsersDataTable::class)
            ->assertSet('perPage', 20) // Valeur par défaut
            ->set('perPage', 50)
            ->assertSet('perPage', 50)
            ->set('perPage', 100)
            ->assertSet('perPage', 100);
    }
}