<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SensoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sensores')->insert([
            ['tipo_sensor' => 'luz', 'nombre_amigable' => 'Luz', 'unidad' => 'lux'],
            ['tipo_sensor' => 'humedad-ambiental', 'nombre_amigable' => 'Humedad', 'unidad' => '%'],
            ['tipo_sensor' => 'temperatura-infrarojo', 'nombre_amigable' => 'Temperatura Corporal', 'unidad' => '°C'],
            ['tipo_sensor' => 'temperatura-ambiental', 'nombre_amigable' => 'Temperatura Ambiental', 'unidad' => '°C'],
            ['tipo_sensor' => 'vibracion', 'nombre_amigable' => 'Vibraciones', 'unidad' => 'Hz'],
            ['tipo_sensor' => 'sonido', 'nombre_amigable' => 'Sonido', 'unidad' => 'dB'],
            ['tipo_sensor' => 'movimiento', 'nombre_amigable' => 'Movimiento', 'unidad' => ''],
        ]);
    }
}
