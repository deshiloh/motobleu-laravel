<?php

namespace Database\Factories;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class AdresseEntrepriseFactory extends Factory
{
    protected $model = AdresseEntreprise::class;

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
            'type' => AdresseEntrepriseTypeEnum::FACTURATION,
            'email' => $this->faker->email,
            'nom' => 'Facturation',
            'tva' => Str::random(10)
        ];
    }
}
