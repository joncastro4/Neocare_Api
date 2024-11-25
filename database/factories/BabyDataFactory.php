<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BabyDataFactory extends Factory
{
    public function definition()
    {
        return [
            'baby_incubator_id' => $this->faker->numberBetween(1, 10),
            'oxygen' => $this->faker->numberBetween(0, 100),
            'heart_rate' => $this->faker->numberBetween(0, 100),
            'temperature' => $this->faker->numberBetween(0, 100),
            'ambient_temperature' => $this->faker->numberBetween(0, 100),
            'humidity' => $this->faker->numberBetween(0, 100),
            'sound' => $this->faker->numberBetween(0, 1),
            'light' => $this->faker->numberBetween(0, 100),
            'vibration' => $this->faker->numberBetween(0, 1),
            'movement' => $this->faker->numberBetween(0, 1),
        ];
    }
}
