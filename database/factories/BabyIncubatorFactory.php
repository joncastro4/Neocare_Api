<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BabyIncubator;
use App\Models\Baby;
use App\Models\Incubator;
use App\Models\Nurse;

class BabyIncubatorFactory extends Factory
{

    protected $model = BabyIncubator::class;
    public function definition()
    {
        return [
            'baby_id' => Baby::factory(),
            'incubator_id' => Incubator::factory(),
            'nurse_id' => Nurse::factory(),
            'egress_date' => $this->faker->date(),
        ];
    }
}
