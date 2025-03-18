<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Nurse;
use App\Models\UserPerson;
use App\Models\Hospital;
class NurseFactory extends Factory
{
    protected $model = Nurse::class;
    public function definition()
    {
        return [
            'hospital_id' => Hospital::factory(),
            'user_person_id' => UserPerson::factory(),
            'rfc' => $this->faker->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'image_path' => $this->faker->imageUrl
        ];
    }
}
