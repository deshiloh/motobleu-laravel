<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Localisation>
 */
class LocalisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->streetName,
            'adresse' => $this->faker->streetAddress,
            'adresse_complement' => $this->faker->secondaryAddress,
            'code_postal' => $this->faker->postcode,
            'ville' => $this->faker->city,
            'telephone' => $this->faker->phoneNumber,
            'is_actif' => true,
        ];
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
