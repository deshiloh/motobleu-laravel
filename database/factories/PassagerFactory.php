<?php

namespace Database\Factories;

use App\Models\Passager;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class PassagerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->name,
            'portable' => $this->faker->phoneNumber,
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->email
        ];
    }
}
