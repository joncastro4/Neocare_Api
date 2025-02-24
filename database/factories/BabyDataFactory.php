<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BabyIncubator;
use App\Models\BabyData;

class BabyDataFactory extends Factory
{
    protected $model = BabyData::class;
    public function definition()
    {
        return [
            'baby_incubator_id' => BabyIncubator::factory(),
            'oxygen' => $this->faker->numberBetween(0, 100),
            'heart_rate' => $this->faker->numberBetween(0, 100),
            'temperature' => $this->faker->numberBetween(0, 100),
            'ambient_temperature' => $this->faker->numberBetween(0, 100),
            'humidity' => $this->faker->numberBetween(0, 100),
            'sound' => $this->faker->numberBetween(0, 1),
            'light' => $this->faker->numberBetween(0, 100),
            'vibration' => $this->faker->numberBetween(0, 1),
            'movement' => $this->faker->numberBetween(0, 1),
        ];
    }
}
