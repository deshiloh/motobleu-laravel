<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntrepriseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @var Collection|HasFactory|Model|mixed
     */
    private mixed $entreprise;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);
        $this->actingAs($user);

        $this->entreprise = Entreprise::find(1);
    }

    public function testAcessListEntreprises()
    {
        $response = $this->get(route('admin.entreprises.index'));
        $response->assertStatus(200);
    }

    public function testCanAccessShowPageEntreprise()
    {
        $response = $this->get(route('admin.entreprises.show', ['entreprise' => $this->entreprise->id]));
        $response->assertStatus(200);
    }

    public function testAccessCreateFormEntreprise()
    {
        $response = $this->get(route('admin.entreprises.create'));
        $response->assertStatus(200);
    }

    public function testStoreEntreprise()
    {
        $response = $this->post(route('admin.entreprises.store'), [
            'nom' => 'test'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('entreprises', [
            'nom' => 'test'
        ]);
    }

    public function testAccessEditFormEnteprise()
    {
        $response = $this->get(route('admin.entreprises.edit', [
            'entreprise' => $this->entreprise->id
        ]));
        $response->assertStatus(200);
    }

    public function testUpdateEntreprise()
    {
        $response = $this->put(
            route('admin.entreprises.update', ['entreprise' => $this->entreprise]),
            [
                'nom' => 'toto'
            ]
        );
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('entreprises', ['nom' => 'toto']);
    }

    public function testDeleteEntreprise()
    {
        $response = $this->delete(route('admin.entreprises.destroy', ['entreprise' => $this->entreprise]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('entreprises', $this->entreprise->toArray());
    }
}
