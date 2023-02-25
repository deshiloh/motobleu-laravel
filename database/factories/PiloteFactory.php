<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pilote>
 */
class PiloteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'adresse' => $this->faker->address,
            'adresse_complement' => $this->faker->secondaryAddress,
            'code_postal' => $this->faker->postcode,
            'ville' => $this->faker->city,
            'is_actif' => true,
            'entreprise' => $this->faker->company
        ];
    }
}
