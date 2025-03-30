<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Baby;
use App\Models\Person;
use App\Models\Hospital;

class BabyFactory extends Factory
{
    protected $model = Baby::class;
    public function definition()
    {
        return [
            'hospital_id' => 1,
            'person_id' => Person::factory(),
            'date_of_birth' => $this->faker->date(),
        ];
    }
}
