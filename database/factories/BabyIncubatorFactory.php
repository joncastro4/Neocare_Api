<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BabyIncubator;
use App\Models\Baby;
use App\Models\Incubator;

class BabyIncubatorFactory extends Factory
{

    protected $model = BabyIncubator::class;
    public function definition()
    {
        return [
            'baby_id' => Baby::factory(),
            'incubator_id' => Incubator::factory(),
        ];
    }
}
