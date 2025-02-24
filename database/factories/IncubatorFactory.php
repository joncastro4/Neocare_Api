<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Incubator;
use App\Models\Room;
class IncubatorFactory extends Factory
{
    protected $model = Incubator::class;
    public function definition()
    {
        return [
            'room_id' => Room::factory(),
            'state' => $this->faker->randomElement(['active', 'available']),
        ];
    }
}
