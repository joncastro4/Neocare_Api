<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MongoSensor;

class MongoSensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MongoSensor::create([
            'type' => 'temperature',
            'unit' => '°C',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 0,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);

        MongoSensor::create([
            'type' => 'humidity',
            'unit' => '%',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 0,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);

        MongoSensor::create([
            'type' => 'light',
            'unit' => 'lumen',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 0,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);

        MongoSensor::create([
            'type' => 'motion',
            'unit' => 'boolean',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 1,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);

        MongoSensor::create([
            'type' => 'vibration',
            'unit' => 'Hz',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 0,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);

        MongoSensor::create([
            'type' => 'sound',
            'unit' => 'dB',
            'current_value' => 0,
            'min_value' => 0,
            'max_value' => 0,
            'reading_date' => now(),
            'created_at' => now(),
            'updated_at' => null
        ]);
    }
}
