<?php

namespace Tests\Feature;

use App\Models\Localisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocalisationTest extends TestCase
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
    private mixed $localisation;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);
        $this->actingAs($user);

        $this->localisation = Localisation::find(1);
    }

    public function testAcessList()
    {
        $response = $this->get(route('admin.localisations.index'));
        $response->assertStatus(200);
    }

    public function testAccessCreateFormLocalisation()
    {
        $response = $this->get(route('admin.localisations.create'));
        $response->assertStatus(200);
    }

    public function testCanCreateLocalisation()
    {
        $localisation = Localisation::factory()->make()->toArray();

        $response = $this->post(route('admin.localisations.store'), $localisation);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('localisations', $localisation);
    }

    public function testCreateWithWrongData()
    {
        $localisation = Localisation::factory([
            'nom' => '',
            'adresse' => ''
        ])->make()->toArray();

        $response = $this->post(route('admin.localisations.store'), $localisation);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom', 'adresse']);
        $this->assertDatabaseMissing('localisations', $localisation);
    }

    public function testCanAccessEditLocalisationForm()
    {
        $response = $this->get(route('admin.localisations.edit', [
            'localisation' => $this->localisation->id
        ]));
        $response->assertStatus(200);
    }

    public function testCanEditLocalisation()
    {
        $datas = Localisation::factory()->nonActif()->make()->toArray();
        $response = $this->put(route('admin.localisations.update', [
            'localisation' => $this->localisation->id
        ]), $datas);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('localisations', $datas);
    }

    public function testCanDeleteLocalisation()
    {
        $response = $this->delete(route('admin.localisations.destroy', [
            'localisation' => $this->localisation->id
        ]));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('localisations', $this->localisation->toArray());
    }
}
