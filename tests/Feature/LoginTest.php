<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory([
            'email' => 'test@test.com',
            'password' => Hash::make('test')
        ])->create();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this->post(route('login'), [
            'email' => 'test@test.com',
            'password' => 'test'
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function testLoginFailWithWrongEmail()
    {
        $response = $this->post(route('login'), [
            'email' => 'test',
            'password' => 'test'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function testLoginNotMatchRecord()
    {
        $response = $this->post(route('login'), [
            'email' => 'test2@test.com',
            'password' => 'test'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function testLogout()
    {
        $response = $this->get(route('logout'));
        $response->assertStatus(302);
    }

    public function testAccessForgotPasswordForm()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    public function testRequestForgotPassword()
    {
        $response = $this->post(route('password.email', ['email' => 'test@test.com']));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
    }
}
