<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Room;
use App\Models\Hospital;
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Room::class;
    public function definition()
    {
        return [
            'hospital_id' => 1,
            'name' => $this->faker->name(),
            'number' => $this->faker->unique()->buildingNumber(),
        ];
    }
}
