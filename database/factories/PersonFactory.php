<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'last_name_1' => $this->faker->lastName(),
            'last_name_2' => $this->faker->lastName(),
        ];
    }
}
