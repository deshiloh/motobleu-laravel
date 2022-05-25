<?php

namespace Database\Factories;

use App\Models\Entreprise;
use App\Models\Passager;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'adresse' => $this->faker->streetAddress,
            'adresse_bis' => $this->faker->streetName,
            'code_postal' => '34000',
            'ville' => 'Montpellier',
            'telephone' => $this->faker->phoneNumber(),
            'is_admin_ardian' => false,
            'is_actif' => true,
            'entreprise_id' => Entreprise::factory()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $passager = Passager::factory()->count(4)->make()->toArray();
            $user->passagers()->createMany($passager);
        });
    }
}
