<?php

namespace Tests\Feature;

use App\Http\Livewire\Auth\ForgotPasswordForm;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Mockery\Mock;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \JsonException
     */
    public function testLogin()
    {
        $user = User::factory()->create();
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'test'
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function testLoginWithNonActifAccount()
    {
        $user = User::factory()->nonActif()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'test'
        ]);

        $response->assertSessionHasErrors(['email' => 'Ce compte est désactivé']);
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
            'email' => 'test23@test.com',
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

    public function testRequestForgotPasswordWithErrors()
    {
        Livewire::test(ForgotPasswordForm::class)
            ->set('email', 'test')
            ->call('resetAction')
            ->assertHasErrors([
                'email' => 'email'
            ]);
    }

    public function testRequestForgotPasswordWithErrorsRequired()
    {
        Livewire::test(ForgotPasswordForm::class)
            ->set('email', '')
            ->call('resetAction')
            ->assertHasErrors([
                'email' => 'required'
            ]);
    }

    public function testRequestForgotPasswordWithWrongUser()
    {
        Notification::fake();

        $user = User::factory([
            'email' => 'user@local.com'
        ])->create();

        Livewire::test(ForgotPasswordForm::class)
            ->set('email', 'toto@test.com')
            ->call('resetAction')
            ->assertHasNoErrors();

        Notification::assertNothingSent($user, ResetPassword::class);
    }

    public function testRequestForgotPasswordOk()
    {
        Notification::fake();

        /** @var User $user */
        $user = User::find(1);

        Livewire::test(ForgotPasswordForm::class)
            ->set('email', $user->email)
            ->call('resetAction')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
