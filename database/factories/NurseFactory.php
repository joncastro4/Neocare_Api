<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NurseFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 10),
            'person_id' => $this->faker->numberBetween(21, 30),
            'rfc' => $this->faker->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'image_path' => $this->faker->imageUrl
        ];
    }
}
