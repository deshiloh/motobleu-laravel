<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|HasFactory|Model|mixed
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory([
            'nom' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('test')
        ])->create();

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

    public function testAddAccountSuccess()
    {
        $entreprise = Entreprise::factory()
            ->create();

        $user = User::factory(['nom' => 'toto'])
            ->make()
            ->toArray();

        $user['entreprise'] = $entreprise->id;

        $response = $this->post(route('admin.accounts.store'), $user);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'nom' => 'toto',
            'entreprise_id' => $entreprise->id
        ]);
    }

    public function testUpdateAccount()
    {
        $entreprise = Entreprise::factory()->create();

        $user = User::factory(['nom' => 'toto'])->make()->toArray();
        $user['entreprise'] = $entreprise->id;

        $response = $this->put(route('admin.accounts.update', ['account' => $this->user->id]), $user);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'nom' => 'toto',
            'entreprise_id' => $entreprise->id
        ]);
    }

    public function testAccessPasswordForm()
    {
        $response = $this->get(route('admin.accounts.password.edit', ['account' => $this->user]));
        $response->assertStatus(200);
    }

    public function testEditPassword()
    {
        $response = $this->put(route('admin.accounts.password.update', [
            'account' => $this->user,
            'password' => 'test2'
        ]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
    }

    public function testDeletedAccount()
    {
        $response = $this->delete(route('admin.accounts.destroy', ['account' => $this->user]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['is_actif' => false]);
    }
}
