<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncubatorFactory extends Factory
{
    public function definition()
    {
        return [
            'state' => $this->faker->randomElement(['active', 'available', 'inactive']),
        ];
    }
}
