<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BabyFactory extends Factory
{
    public function definition()
    {
        return [
            'person_id' => $this->faker->numberBetween(1, 10),
            'date_of_birth' => $this->faker->date(),
            'ingress_date' => $this->faker->date(),
            'egress_date' => $this->faker->date(),
        ];
    }
}
