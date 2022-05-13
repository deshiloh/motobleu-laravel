<?php

namespace Tests\Feature;

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

    public function testAddAccountSuccess()
    {
        $response = $this->post(route('admin.accounts.store'), [
            'nom' => 'test',
            'prenom' => 'test',
            'email' => 'test@test.com',
            'telephone' => '0489574841',
            'adresse' => 'test',
            'adresse_bis' => 'test',
            'code_postal' => '34000',
            'ville' => 'montpellier'
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'nom' => 'test'
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
}
