<?php

namespace Tests\Feature;

use App\Http\Livewire\Account\AccountForm;
use App\Http\Livewire\Account\EditPasswordForm;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AccountTest extends TestCase
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
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::find(1);

        $this->actingAs($this->user);
    }

    public function testAccessAccountPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('admin.accounts.index'));
        $response->assertStatus(200);
    }

    public function testAccessCreatePageAccount()
    {
        $response = $this->get(route('admin.accounts.create'));
        $response->assertStatus(200);
    }

    public function testCanAccessEditAccountPage()
    {
        $response = $this->get(route('admin.accounts.edit', ['account' => $this->user->id]));
        $response->assertStatus(200);
    }

    public function testCreateAccountWithErrors()
    {
        Livewire::test(AccountForm::class)
            ->set('user.nom', '')
            ->set('user.prenom', '')
            ->set('user.email', '')
            ->set('user.telephone', '')
            ->set('user.adresse', '')
            ->set('user.adresse_bis', '')
            ->set('user.code_postal', '')
            ->set('user.ville', '')
            ->set('user.entreprise_id', null)
            ->set('user.is_actif', true)
            ->set('user.is_admin_ardian', false)
            ->call('save')
            ->assertHasErrors([
                'user.nom' => 'required',
                'user.prenom' => 'required',
                'user.email' => 'required',
                'user.telephone' => 'required',
                'user.adresse' => 'required',
                'user.code_postal' => 'required',
                'user.ville' => 'required',
                'user.entreprise_id' => 'required',
            ]);
    }

    public function testAddAccountSuccess()
    {
        $entreprise = Entreprise::factory()->create();
        $user = User::factory()->make();
        Livewire::test(AccountForm::class)
            ->set('user.nom', $user->nom)
            ->set('user.prenom', $user->prenom)
            ->set('user.email', $user->email)
            ->set('user.telephone', $user->telephone)
            ->set('user.adresse', $user->adresse)
            ->set('user.adresse_bis', $user->adresse_bis)
            ->set('user.code_postal', $user->code_postal)
            ->set('user.ville', $user->ville)
            ->set('user.entreprise_id', $entreprise->id)
            ->set('user.is_actif', true)
            ->set('user.is_admin_ardian', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(User::where('nom', $user->nom)->exists());
    }

    public function testUpdateAccount()
    {
        $userExist = User::find(1);
        $user = User::factory()->make();

        Livewire::test(AccountForm::class, ['user' => $userExist])
            ->set('user.nom', 'test')
            ->set('user.prenom', $user->prenom)
            ->set('user.email', $user->email)
            ->set('user.telephone', $user->telephone)
            ->set('user.adresse', $user->adresse)
            ->set('user.adresse_bis', $user->adresse_bis)
            ->set('user.code_postal', $user->code_postal)
            ->set('user.ville', $user->ville)
            ->set('user.entreprise_id', $userExist->entreprise_id)
            ->set('user.is_actif', true)
            ->set('user.is_admin_ardian', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(User::where('nom', 'test')->exists());
    }

    public function testAccessPasswordForm()
    {
        $response = $this->get(route('admin.accounts.password.edit', ['account' => $this->user]));
        $response->assertStatus(200);
    }

    public function testEditPasswordWithErrors()
    {
        Livewire::test(EditPasswordForm::class)
            ->set('password', '')
            ->call('editAction')
            ->assertHasErrors([
                'password' => 'required'
            ]);
    }

    public function testEditPasswordOk()
    {
        $user = User::find(1);

        Livewire::test(EditPasswordForm::class, ['user' => $user])
            ->set('password', 'test')
            ->call('editAction')
            ->assertHasNoErrors();
    }

    public function testDeletedAccount()
    {
        $response = $this->delete(route('admin.accounts.destroy', ['account' => $this->user]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['is_actif' => false]);
    }
}
