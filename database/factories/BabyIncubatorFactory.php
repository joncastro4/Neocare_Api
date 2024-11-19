<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BabyIncubatorFactory extends Factory
{

    public function definition()
    {
        return [
            'baby_id' => $this->faker->numberBetween(1, 10),
            'incubator_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
