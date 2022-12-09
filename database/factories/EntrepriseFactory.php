<?php

namespace Database\Factories;

use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\TypeFacturation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class EntrepriseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->company,
            'responsable_name' => $this->faker->name,
        ];
    }
}
