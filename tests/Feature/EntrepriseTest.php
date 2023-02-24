<?php

namespace Tests\Feature;

use App\Http\Livewire\Entreprise\EntrepriseForm;
use App\Http\Livewire\Entreprise\EntreprisesDataTable;
use App\Http\Livewire\Entreprise\UsersEntrepriseDataTable;
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

        /** @var USer $user */
        $user = User::find(1);
        $user->assignRole('super admin');

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
                'entreprise.nom' => 'required'
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

    public function testDisableEntreprise(): void
    {
        $entreprise = Entreprise::find(1);

        Livewire::test(EntreprisesDataTable::class)
            ->call('disableEntreprise', $entreprise)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('entreprises', [
            'nom' => $entreprise->nom,
            'is_actif' => false
        ]);
    }

    public function testEnableEntreprise()
    {
        $entreprise = Entreprise::factory([
            'nom' => 'test',
            'is_actif' => false
        ])->create();

        Livewire::test(EntreprisesDataTable::class)
            ->call('enableEntreprise', $entreprise)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('entreprises', [
            'nom' => 'test',
            'is_actif' => true
        ]);
    }

    public function testAttachUserInEntreprise()
    {
        $entrepriseSelected = Entreprise::find(1);
        $userSelected = User::find(10);

        Livewire::test(UsersEntrepriseDataTable::class, ['entreprise' => $entrepriseSelected])
            ->set('userId', $userSelected->id)
            ->call('attach')
            ->assertStatus(200)
            ->assertHasNoErrors();
    }

    public function testDettachUserInEntreprise()
    {
        $entrepriseSelected = Entreprise::find(1);
        $userSelected = User::find(10);

        Livewire::test(UsersEntrepriseDataTable::class, ['entreprise' => $entrepriseSelected])
            ->set('userId', $userSelected->id)
            ->call('detach')
            ->assertStatus(200)
            ->assertHasNoErrors();
    }
}
