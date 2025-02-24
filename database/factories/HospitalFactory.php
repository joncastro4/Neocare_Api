<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hospital;
use App\Models\Address;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hospital>
 */
class HospitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Hospital::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone_number' => substr($this->faker->phoneNumber(), 0, 10),
            'address_id' => Address::factory(),
        ];
    }
}
