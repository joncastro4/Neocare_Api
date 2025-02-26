<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Relative;
use App\Models\Person;
use App\Models\Baby;

class RelativeFactory extends Factory
{
    protected $model = Relative::class;
    public function definition()
    {
        return [
            'person_id' => Person::factory(),
            'baby_id' => Baby::factory(),
            'phone_number' => substr($this->faker->phoneNumber, 0, 10),
            'email' => $this->faker->email,
        ];
    }
}
