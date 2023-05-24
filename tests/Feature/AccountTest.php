<?php

namespace Tests\Feature;

use App\Http\Livewire\Account\AccountForm;
use App\Http\Livewire\Account\EditPasswordForm;
use App\Http\Livewire\Account\EntrepriseForm;
use App\Http\Livewire\Account\UsersDataTable;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->user = User::factory()->create();
        $this->user->assignRole('super admin');

        $this->actingAs($this->user);
    }

    public function testAccessAccountPage(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('admin.accounts.index'));
        $response->assertStatus(200);
    }

    public function testAccessCreatePageAccount(): void
    {
        $response = $this->get(route('admin.accounts.create'));
        $response->assertStatus(200);
    }

    public function testCanAccessEditAccountPage(): void
    {
        $response = $this->get(route('admin.accounts.edit', ['account' => $this->user->id]));
        $response->assertStatus(200);
    }

    public function testRoleUserCantAccessIndexPage()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('admin.accounts.index'));
        $response->assertStatus(403);
    }

    public function testRoleUserCantAccessCreatePage()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('admin.accounts.create'));
        $response->assertStatus(403);
    }

    public function testRoleUserCantAccessEditPage()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('admin.accounts.edit', ['account' => $this->user->id]));
        $response->assertStatus(403);
    }

    public function testCreateAccountWithErrors(): void
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
            ->set('user.is_actif', true)
            ->set('user.is_admin', false)
            ->call('save')
            ->assertHasErrors([
                'user.email' => 'required',
                'user.nom' => 'required',
                'user.prenom' => 'required'
            ]);
    }

    public function testAddAccountSuccess(): void
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
            ->set('user.is_actif', true)
            ->set('user.is_admin', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertTrue(User::where('nom', $user->nom)->exists());
    }

    public function testUpdateAccount(): void
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
            ->set('user.is_actif', true)
            ->set('user.is_admin', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'nom' => 'test',
            'email' => $user->email
        ]);
    }

    public function testAccessPasswordForm(): void
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

    public function testAccessEntrepriseEdit()
    {
        $response = $this->get(route('admin.accounts.entreprise.edit', ['account' => $this->user->id]));
        $response->assertStatus(200);
    }

    public function testAttachEntreprise()
    {
        Livewire::test(EntrepriseForm::class, ['account' => $this->user])
            ->set('entreprises', [4,5])
            ->call('save')
            ->assertHasNoErrors()
        ;

        $this->assertDatabaseHas('entreprise_user', [
            'entreprise_id' => 5,
            'user_id' => $this->user->id
        ]);

        $this->assertDatabaseHas('entreprise_user', [
            'entreprise_id' => 4,
            'user_id' => $this->user->id
        ]);
    }

    public function testDetachEntreprise()
    {
        $user = User::find(1);
        $this->assertDatabaseHas('entreprise_user', [
            'entreprise_id' => 1,
            'user_id' => $user->id
        ]);

        Livewire::test(EntrepriseForm::class, ['account' => $user])
            ->call('detach', Entreprise::find(1))
        ;

        $this->assertDatabaseMissing('entreprise_user', [
            'entreprise_id' => 1,
            'user_id' => $user->id
        ]);
    }

    public function testDisableAccount()
    {
        $user = User::find(1);

        Livewire::test(UsersDataTable::class)
            ->call('disableAccount', $user)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'nom' => $user->nom,
            'is_actif' => false
        ]);
    }

    public function testEnableAccount()
    {
        $user = User::factory([
            'nom' => 'test',
            'is_actif' => false
        ])->create();

        Livewire::test(UsersDataTable::class)
            ->call('enableAccount', $user)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'nom' => 'test',
            'is_actif' => true
        ]);
    }

    public function testSearchAccount()
    {
        User::factory([
            'nom' => 'test'
        ])->create();

        Livewire::test(UsersDataTable::class)
            ->set('search', 'test')
            ->assertSee('test')
            ->assertHasNoErrors()
            ->assertStatus(200);
    }

    public function testSearchWithEntreprise()
    {
        Livewire::test(UsersDataTable::class)
            ->set('selectedEntreprise', 1)
            ->assertSee('test')
            ->assertHasNoErrors()
            ->assertStatus(200);
    }
}
