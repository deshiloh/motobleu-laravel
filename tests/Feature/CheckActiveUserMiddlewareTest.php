<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CheckActiveUserMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testActiveUserCanAccessProtectedRoutes()
    {
        $user = User::find(1);
        $this->assertTrue($user->is_actif);

        $response = $this->actingAs($user)
            ->get(route('front.reservation.list'));

        $response->assertStatus(200);
        $this->assertTrue(Auth::check());
    }

    public function testInactiveUserIsLoggedOutAndRedirected()
    {
        $user = User::factory()->nonActif()->create();
        $user->assignRole('super admin'); // Pour contourner les autres middlewares
        
        $this->assertFalse($user->is_actif);

        $response = $this->actingAs($user)
            ->get(route('admin.homepage'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        $this->assertFalse(Auth::check());
    }

    public function testErrorMessageIsDisplayedOnLoginPage()
    {
        // Tester directement l'affichage de la page de login avec un message d'erreur
        $response = $this->withSession(['error' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'])
            ->get(route('login.form'));
            
        $response->assertStatus(200);
        $response->assertSee('Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
        $response->assertSee('Erreur :');
    }

    public function testGuestUserIsNotAffectedByMiddleware()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $this->assertFalse(Auth::check());
    }
}