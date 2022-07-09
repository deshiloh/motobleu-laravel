<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facture>
 */
class FactureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $currentDate = Carbon::now();
        return [
            'reference' => 'FA'.$currentDate->year.'-'.$currentDate->month.'-1',
            'montant_ht' => $this->faker->randomFloat('2'),
            'tva' => 10,
            'adresse_client' => $this->faker->address,
            'adresse_facturation' => $this->faker->address,
            'month' => $this->faker->month,
            'year' => $this->faker->year,
            'is_acquitte' => false,
        ];
    }
}
