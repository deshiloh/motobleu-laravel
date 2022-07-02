<?php

namespace Database\Factories;

use App\Models\CostCenter;
use App\Models\Passager;
use App\Models\TypeFacturation;
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
            'email' => $this->faker->email,
            'is_actif' => true,
            'cost_center_id' => CostCenter::factory(),
            'type_facturation_id' => TypeFacturation::factory()
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
