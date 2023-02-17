<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->jobTitle(),
            'subtitle' => $this->faker->sentence(1),
            'body' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['Publicado', 'Borrador']),
        ];
    }
}
