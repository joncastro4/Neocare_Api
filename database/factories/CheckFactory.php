<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Check;
use App\Models\Nurse;
use App\Models\BabyIncubator;


class CheckFactory extends Factory
{
    protected $model = Check::class;
    public function definition()
    {
        return [
            'nurse_id' => Nurse::factory(),
            'baby_incubator_id' => BabyIncubator::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
        ];
    }
}
