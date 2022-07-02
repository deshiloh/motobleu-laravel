<?php

namespace Database\Factories;

use App\Models\CostCenter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class CostCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => 'S'.rand(0, 999).' '.$this->faker->company,
            'is_actif' => true
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
