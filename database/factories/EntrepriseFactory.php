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
    public function definition()
    {
        return [
            'nom' => $this->faker->company
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Entreprise $entreprise) {
            $entreprise->adresseEntreprises()
                ->save(AdresseEntreprise::factory()->create());

            $entreprise->typeFacturations()
                ->saveMany(TypeFacturation::factory()->count(2)->create());
        });
    }
}
