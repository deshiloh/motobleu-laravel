<?php

namespace Database\Factories;

use App\Models\AdresseReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdresseReservation>
 */
class AdresseReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'adresse' => $this->faker->streetAddress,
            'adresse_complement' => $this->faker->secondaryAddress,
            'code_postal' => $this->faker->postcode,
            'ville' => $this->faker->city,
        ];
    }
}
