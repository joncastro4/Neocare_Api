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
            ['tipo_sensor' => 'bpm', 'nombre_amigable' => 'Frecuencia Cardíaca', 'unidad' => 'bpm'],
            ['tipo_sensor' => 'fotoresistencia', 'nombre_amigable' => 'Fotoresistencia', 'unidad' => 'lux'],
            ['tipo_sensor' => 'humedad', 'nombre_amigable' => 'Humedad', 'unidad' => '%'],
            ['tipo_sensor' => 'oxigeno', 'nombre_amigable' => 'Oxígeno', 'unidad' => '%'],
            ['tipo_sensor' => 'rgb', 'nombre_amigable' => 'Color RGB', 'unidad' => ''],
            ['tipo_sensor' => 'temperaturacorporal', 'nombre_amigable' => 'Temperatura Corporal', 'unidad' => '°C'],
            ['tipo_sensor' => 'temperaturambiental', 'nombre_amigable' => 'Temperatura Ambiental', 'unidad' => '°C'],
            ['tipo_sensor' => 'vibraciones', 'nombre_amigable' => 'Vibraciones', 'unidad' => 'Hz'],
        ]);
    }
}
