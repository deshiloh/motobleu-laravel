<?php

namespace Tests\Feature;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdresseEntrepriseTest extends TestCase
{
    use RefreshDatabase;

    public $adresseEntreprise;

    protected function setUp(): void
    {
        parent::setUp();

        $entreprise = Entreprise::factory()->create();
        $this->adresseEntreprise = AdresseEntreprise::factory()->make();
        $entreprise->adresseEntreprises()->save($this->adresseEntreprise);
    }

    public function testCanAccessEditForm()
    {
        $response = $this->get(route('admin.entreprises.adresses.edit', [
            'entreprise' => $this->adresseEntreprise->entreprise_id,
            'adress' => $this->adresseEntreprise->id
        ]));
        $response->assertStatus(200);
    }

    public function testEditAdresseEntreprise()
    {
        $datas = AdresseEntreprise::factory(['tva' => 'test'])->make()->toArray();
        unset($datas['type']);

        $response = $this->put(route('admin.entreprises.adresses.update', [
            'entreprise' => $this->adresseEntreprise->entreprise_id,
            'adress' => $this->adresseEntreprise->id
        ]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('adresse_entreprises', ['tva' => 'test']);
    }

    public function testEditAdresseEntrepriseWithWrongDatas()
    {
        $datas = AdresseEntreprise::factory(['email' => 'test'])->make()->toArray();
        unset($datas['type']);

        $response = $this->put(route('admin.entreprises.adresses.update', [
            'entreprise' => $this->adresseEntreprise->entreprise_id,
            'adress' => $this->adresseEntreprise->id
        ]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function testAcessCreateAdresseEntrepriseForm()
    {
        $reponse = $this->get(route('admin.entreprises.adresses.create', [
            'entreprise' => $this->adresseEntreprise->entreprise_id
        ]));
        $reponse->assertStatus(200);
    }

    public function testCreateAdresseEntreprise()
    {
        $datas = AdresseEntreprise::factory([
            'email' => 'test@test.com',
            'type' => AdresseEntrepriseTypeEnum::PHYSIQUE
        ])->make()->toArray();
        unset($datas['entreprise_id']);

        $response = $this->post(route('admin.entreprises.adresses.store', [
            'entreprise' => $this->adresseEntreprise->entreprise_id
        ]), $datas);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('adresse_entreprises', ['email' => 'test@test.com']);
    }

    public function testAdresseEntrepriseTypeAlreadyExist()
    {
        $datas = AdresseEntreprise::factory([
            'email' => 'test@test.com',
        ])->make()->toArray();
        unset($datas['entreprise_id']);

        $response = $this->post(route('admin.entreprises.adresses.store', [
            'entreprise' => $this->adresseEntreprise->entreprise_id
        ]), $datas);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['type']);
    }

    public function testDeleteAdresseEntreprise()
    {
        $response = $this->delete(route('admin.entreprises.adresses.destroy', [
            'entreprise' => $this->adresseEntreprise->entreprise_id,
            'adress' => $this->adresseEntreprise->id
        ]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('adresse_entreprises', $this->adresseEntreprise->toArray());
    }
}
