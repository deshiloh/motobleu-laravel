<?php

use App\Http\Livewire\Auth\ForgotPasswordForm;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get,post};
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

test("Can access to the login page", function() {
    get(route('login.form'))
        ->assertStatus(200);
});

test("Can login super user", function() {
    $this->seed();

    post(route('login'), [
        'email' => 'test1@test.com',
        'password' => 'test'
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.homepage'))
    ;
});

test("Can login user", function() {
    $this->seed();

    post(route('login'), [
        'email' => 'test2@test.com',
        'password' => 'test'
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('front.reservation.list'))
    ;
})->skip();

test("Login error email not good format", function() {
    post(route('login'), [
        'email' => 'test@tesm',
        'password' => 'test'
    ])->assertSessionHasErrors(['email']);
});

test("Login error password is missing", function() {
    post(route('login'), [
        'email' => 'test@test.com',
        'password' => ''
    ])->assertSessionHasErrors(['password']);
});

test("Access forgot password page", function() {
    get(route('password.request'))
        ->assertStatus(200);
});

test("Request update password validation", function() {
    post(route('password.update'), [
        'token' => '',
        'email' => '',
        'password' => '',
    ])
        ->assertSessionHasErrors([
            'email',
            'password',
            'token'
        ]);
});

test("Error when reset password send with no email", function() {
    livewire(ForgotPasswordForm::class)
        ->call('resetAction')
        ->assertHasErrors(['email'])
    ;
});

test("Send password reset works", function() {
    livewire(ForgotPasswordForm::class)
        ->set('email', 'test1@test.com')
        ->call('resetAction')
        ->assertHasNoErrors();
});

test("Request update password success", function() {
    // Crée un utilisateur
    $user = User::factory()->create(['email' => 'test@example.com']);

    // Demande une réinitialisation de mot de passe
    Notification::fake();
    $user->notify(new ResetPassword(Password::broker()->createToken($user)));

    // Récupère le token de réinitialisation de mot de passe
    $token = DB::table('password_resets')->where('email', $user->email)->first()->token;

    // Simule la réinitialisation de mot de passe
    $newPassword = 'newpassword123';
    $response = post(route('password.update'), [
        'email' => $user->email,
        'token' => $token,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    // Vérifie que le mot de passe a été réinitialisé avec succès
    $response->assertStatus(302)->assertRedirect('/');
});


