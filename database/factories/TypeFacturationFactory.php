<?php

namespace Database\Factories;

use App\Models\Entreprise;
use App\Models\TypeFacturation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class TypeFacturationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => 'Ardian ' . Str::random(8),
            'is_actif' => true
        ];
    }
}
