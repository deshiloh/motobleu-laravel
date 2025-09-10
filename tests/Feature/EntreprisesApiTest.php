<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntreprisesApiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testEntreprisesApiReturnsCorrectData()
    {
        $user = User::find(1);
        $user->assignRole('super admin');

        $response = $this->actingAs($user)
            ->getJson(route('api.entreprises'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'nom']
        ]);

        // Vérifier que l'API retourne bien des données
        $data = $response->json();
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('nom', $data[0]);
    }

    public function testEntreprisesApiWithSearchParameter()
    {
        $user = User::find(1);
        $user->assignRole('super admin');

        // Créer des entreprises pour le test
        $entreprise1 = Entreprise::factory()->create([
            'nom' => 'Unique Search Term',
            'is_actif' => true
        ]);
        
        $entreprise2 = Entreprise::factory()->create([
            'nom' => 'Other Company',
            'is_actif' => true
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('api.entreprises', ['search' => 'Unique']));

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // Vérifier que seule l'entreprise avec "Unique" est retournée
        $this->assertCount(1, $data);
        $this->assertEquals('Unique Search Term', $data[0]['nom']);
    }
}