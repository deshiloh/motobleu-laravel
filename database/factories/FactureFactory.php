<?php

namespace Database\Factories;

use App\Enum\BillStatut;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facture>
 */
class FactureFactory extends Factory
{
    static int $ref = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $currentDate = Carbon::now();
        return [
            'reference' => 'FA'.$currentDate->year.'-'.$currentDate->month.'-' . self::$ref ++,
            'statut' => BillStatut::CREATED->value,
            'montant_ttc' => $this->faker->randomFloat('2', 10, 99999),
            'montant_tva' => $this->faker->randomFloat('2', 10, 99999),
            'tva' => 10,
            'adresse_client' => $this->faker->address,
            'adresse_facturation' => $this->faker->address,
            'month' => $currentDate->month,
            'year' => $currentDate->year,
            'is_acquitte' => false,
        ];
    }
}
