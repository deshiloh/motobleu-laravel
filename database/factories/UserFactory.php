<?php

namespace Database\Factories;

use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    static int $iteration = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => 'test'.self::$iteration ++ . '@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test'),
            'remember_token' => Str::random(10),
            'adresse' => $this->faker->streetAddress,
            'adresse_bis' => $this->faker->streetName,
            'code_postal' => '34000',
            'ville' => 'Montpellier',
            'telephone' => $this->faker->phoneNumber(),
            'is_admin_ardian' => false,
            'is_actif' => true
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

    public function nonActif()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_actif' => false
            ];
        });
    }
}
