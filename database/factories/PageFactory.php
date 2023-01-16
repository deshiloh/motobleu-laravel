<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Je suis un test',
            'content' => 'Ceci est mon contenu',
            'slug' => \Str::slug('Je suis un test')
        ];
    }
}
