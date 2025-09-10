<?php

namespace Tests\Feature;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Livewire\Entreprise\AdresseEntrepriseForm;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdresseEntrepriseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    public $adresseEntreprise;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);

        $user->assignRole('super admin');

        $this->adresseEntreprise = AdresseEntreprise::find(1);

        $this->actingAs($user);
    }

    public function testCreateAdresseEntrepriseWithErrors()
    {
        $entreprise = Entreprise::find(1);

        Livewire::test(AdresseEntrepriseForm::class, ['entreprise' => $entreprise])
            ->set('adresseEntreprise.nom', '')
            ->set('adresseEntreprise.email', '')
            ->set('adresseEntreprise.adresse', '')
            ->set('adresseEntreprise.adresse_complement', '')
            ->set('adresseEntreprise.code_postal', '')
            ->set('adresseEntreprise.ville', '')
            ->set('adresseEntreprise.tva', '')
            ->call('save')
            ->assertHasErrors([
                'adresseEntreprise.nom' => 'required',
                'adresseEntreprise.email' => 'required',
                'adresseEntreprise.adresse' => 'required',
                'adresseEntreprise.code_postal' => 'required',
                'adresseEntreprise.ville' => 'required',
            ]);
    }

    public function testCreateAdresseEntrepriseOk()
    {
        $entreprise = Entreprise::find(1);
        $adresseEntreprise = AdresseEntreprise::factory()->make();

        Livewire::test(AdresseEntrepriseForm::class, ['entreprise' => $entreprise])
            ->set('adresseEntreprise.nom', $adresseEntreprise->nom)
            ->set('adresseEntreprise.email', $adresseEntreprise->email)
            ->set('adresseEntreprise.adresse', $adresseEntreprise->adresse)
            ->set('adresseEntreprise.adresse_complement', $adresseEntreprise->adresse_complement)
            ->set('adresseEntreprise.code_postal', $adresseEntreprise->code_postal)
            ->set('adresseEntreprise.ville', $adresseEntreprise->ville)
            ->set('adresseEntreprise.tva', $adresseEntreprise->tva)
            ->set('adresseEntreprise.type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('adresse_entreprises', [
            'nom' => $adresseEntreprise->nom,
            'email' => $adresseEntreprise->email,
            'entreprise_id' => $entreprise->id
        ]);
    }

    public function testDeleteAdresseEntreprise()
    {
        // Create a new AdresseEntreprise for this test to ensure it exists and can be deleted
        $entreprise = Entreprise::find(1);
        $adresseEntreprise = AdresseEntreprise::factory()->create([
            'entreprise_id' => $entreprise->id
        ]);

        $response = $this->withoutMiddleware()->delete(route('admin.entreprises.adresses.destroy', [
            'entreprise' => $adresseEntreprise->entreprise_id,
            'adress' => $adresseEntreprise->id
        ]));
        
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('adresse_entreprises', [
            'id' => $adresseEntreprise->id
        ]);
    }
}
