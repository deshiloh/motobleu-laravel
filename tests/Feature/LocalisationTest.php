<?php

namespace Tests\Feature;

use App\Http\Livewire\Localisation\LocalisationForm;
use App\Models\Localisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
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

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('super admin');

        $this->actingAs($user);

        $this->localisation = Localisation::find(1);
    }

    public function testAcessList()
    {
        $response = $this->get(route('admin.localisations.index'));
        $response->assertStatus(200);
    }

    public function testCreateLocalisationWithErrors()
    {
        Livewire::test(LocalisationForm::class)
            ->set('localisation.nom', '')
            ->set('localisation.telephone', '')
            ->set('localisation.adresse', '')
            ->set('localisation.adresse_complement', '')
            ->set('localisation.code_postal', '')
            ->set('localisation.ville', '')
            ->set('localisation.is_actif', true)
            ->call('save')
            ->assertHasErrors([
                'localisation.nom' => 'required',
                'localisation.telephone' => 'required',
                'localisation.adresse' => 'required',
                'localisation.code_postal' => 'required',
                'localisation.ville' => 'required',
            ]);
    }

    public function testCreateLocalisationOK()
    {
        $localisation = Localisation::factory()->make();

        Livewire::test(LocalisationForm::class)
            ->set('localisation.nom', $localisation->nom)
            ->set('localisation.telephone', $localisation->telephone)
            ->set('localisation.adresse', $localisation->adresse)
            ->set('localisation.adresse_complement', $localisation->adresse_complement)
            ->set('localisation.code_postal', $localisation->code_postal)
            ->set('localisation.ville', $localisation->ville)
            ->set('localisation.is_actif', $localisation->is_actif)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(Localisation::where('nom', $localisation->nom)->exists());
    }

    public function testEditLocalisationOK()
    {
        $localisation = Localisation::factory()->create();

        Livewire::test(LocalisationForm::class)
            ->set('localisation.nom', 'test')
            ->set('localisation.telephone', $localisation->telephone)
            ->set('localisation.adresse', $localisation->adresse)
            ->set('localisation.adresse_complement', $localisation->adresse_complement)
            ->set('localisation.code_postal', $localisation->code_postal)
            ->set('localisation.ville', $localisation->ville)
            ->set('localisation.is_actif', $localisation->is_actif)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(Localisation::where('nom', 'test')->exists());
    }

    public function testCanDeleteLocalisation()
    {
        $localisation = Localisation::factory()->create();

        $response = $this->delete(route('admin.localisations.destroy', [
            'localisation' => $localisation->id
        ]));

        $localisation->is_actif = false;

        $response->assertStatus(302);
        $this->assertModelExists($localisation);
    }
}
