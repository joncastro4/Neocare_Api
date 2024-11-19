<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class NurseBabyFactory extends Factory
{
    public function definition()
    {
        return [
            'nurse_id' => $this->faker->numberBetween(1, 10),
            'baby_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
