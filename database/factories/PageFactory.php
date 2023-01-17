<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => [
                'fr' => 'Je suis un test',
                'en' => 'im a test'
            ],
            'content' => [
                'fr' => \Faker\Factory::create('fr')->paragraph,
                'en' => \Faker\Factory::create('en')->paragraph,
            ]
        ];
    }
}
