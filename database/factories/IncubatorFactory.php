<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Incubator;

class IncubatorFactory extends Factory
{
    protected $model = Incubator::class;
    public function definition()
    {
        return [
            'state' => $this->faker->randomElement(['active', 'available']),
        ];
    }
}
