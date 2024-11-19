<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RelativeFactory extends Factory
{
    public function definition()
    {
        return [
            'person_id' => $this->faker->numberBetween(int1: 11, int2: 20),
            'baby_id' => $this->faker->numberBetween(1, 10),
            'phone_number' => $this->faker->phoneNumber,
            'contact' => $this->faker->name
        ];
    }
}
