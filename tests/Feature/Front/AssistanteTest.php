<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Account\AccountForm;
use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssistanteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);
        $user->assignRole(['admin', 'super admin']);

        $this->actingAs($user);
    }

    public function testAccessAssistantesList()
    {
        $response = $this->get(route('front.user.list'));

        $response->assertStatus(200);
    }

    public function testAccessCreateAccount()
    {
        $response = $this->get(route('front.user.create'));
        $response->assertStatus(200);
    }

    public function testGuestNotAccess()
    {
        \Auth::logout();

        $response = $this->get(route('front.user.list'));
        $response->assertStatus(302);
    }

    public function testGuestNotAccessCreate()
    {
        \Auth::logout();

        $response = $this->get(route('front.user.create'));
        $response->assertStatus(302);
    }

    public function testGuestNotAccessEdit()
    {
        \Auth::logout();

        $user = User::factory()->create();

        $response = $this->get(route('front.user.edit', ['account' => $user->id]));
        $response->assertStatus(302);
    }

    public function testRoleUserCanNotAccess()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.user.list'));
        $response->assertStatus(403);
    }

    public function testRoleUserCanNotAccessCreate()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.user.create'));
        $response->assertStatus(403);
    }

    public function testRoleUserCanNotAccessEdit()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.user.edit', ['account' => $user->id]));
        $response->assertStatus(403);
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
            ->call('save')
            ->assertHasErrors([
                'user.nom' => 'required',
                'user.prenom' => 'required',
                'user.email' => 'required',
                'user.telephone' => 'required',
                'user.adresse' => 'required',
                'user.code_postal' => 'required',
                'user.ville' => 'required',
            ])
        ;
    }

    public function testCreateAccount()
    {
        Livewire::test(AccountForm::class)
            ->set('user.nom', 'test')
            ->set('user.prenom', 'test')
            ->set('user.email', 'test@test.com')
            ->set('user.telephone', 'test')
            ->set('user.adresse', 'test')
            ->set('user.adresse_bis', '')
            ->set('user.code_postal', 'test')
            ->set('user.ville', 'test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(User::where('nom', 'test')->exists());
    }

    public function testEditAccount()
    {
        /** @var User $user */
        $user = User::factory()->create();
        Livewire::test(AccountForm::class, ['account' => $user])
            ->set('user.nom', 'test')
            ->set('user.prenom', 'test')
            ->set('user.email', 'test@test.com')
            ->set('user.telephone', 'test')
            ->set('user.adresse', 'test')
            ->set('user.adresse_bis', '')
            ->set('user.code_postal', 'test')
            ->set('user.ville', 'test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(User::where('nom', 'test')->exists());
    }
}
