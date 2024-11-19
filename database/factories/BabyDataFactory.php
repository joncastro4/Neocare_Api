<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BabyDataFactory extends Factory
{
    public function definition()
    {
        return [
            'baby_incubator_id' => $this->faker->numberBetween(1, 10),
            'oxigen' => $this->faker->numberBetween(0, 100),
            'heart_rate' => $this->faker->numberBetween(0, 100),
            'temperature' => $this->faker->numberBetween(0, 100),
        ];
    }
}
