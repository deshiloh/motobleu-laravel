<?php

namespace Tests\Feature;

use App\Http\Livewire\Entreprise\EntrepriseForm;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EntrepriseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected bool $seed = true;

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

    public function testAcessListEntreprises(): void
    {
        $response = $this->get(route('admin.entreprises.index'));

        $response->assertStatus(200);
    }

    public function testCanAccessShowPageEntreprise(): void
    {
        $response = $this->get(route('admin.entreprises.show', ['entreprise' => $this->entreprise->id]));

        $response->assertStatus(200);
    }

    public function testCreateEntrepriseWithErrors(): void
    {
        Livewire::test(EntrepriseForm::class)
            ->set('entreprise.nom', '')
            ->set('entreprise.responsable_name', '')
            ->call('save')
            ->assertHasErrors([
                'entreprise.nom' => 'required',
                'entreprise.responsable_name' => 'required'
            ]);
    }

    public function testCreateEntrepriseOK(): void
    {
        Livewire::test(EntrepriseForm::class)
            ->set('entreprise.nom', 'test')
            ->set('entreprise.responsable_name', 'responsable')
            ->set('entreprise.is_actif', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Entreprise::where('nom', 'test')->exists());
    }

    public function testDeleteEntreprise(): void
    {
        $response = $this->delete(route('admin.entreprises.destroy', ['entreprise' => $this->entreprise]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('entreprises', $this->entreprise->toArray());
    }
}
