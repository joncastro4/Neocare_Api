<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;

class AdafruitController extends Controller
{
    private $AIOkey;
    private $AIOuser;

    public function __construct()
    {
        $this->AIOkey = 'aio_CdSd32sWXEBivEDROv09jU4cfXBQ';
        $this->AIOuser = 'Shuy03';
    }

    public $sinDatos = 'Sin datos disponibles';

    public function obtenerTodosLosSensores()
    {
        $sensores = Sensor::whereNot('tipo_sensor', 'rgb')->whereNot('tipo_sensor', 'vibraciones')->get();
        $datalist = [];

        foreach ($sensores as $sensor) {
            try {
                // Obtener datos históricos para el sensor actual
                $response = Http::withHeaders([
                    'X-AIO-Key' => $this->AIOkey,
                ])->get("https://io.adafruit.com/api/v2/{$this->AIOuser}/feeds/incubadora.{$sensor->tipo_sensor}/data");

                if ($response->successful()) {
                    $data = $response->json(); // Datos históricos en formato JSON
                    $values = [];

                    // Filtrar y convertir valores válidos
                    foreach ($data as $entry) {
                        if (isset($entry['value']) && is_numeric($entry['value'])) {
                            $values[] = (float) $entry['value'];
                        }
                    }

                    $value = !empty($data) ? $data[count($data) - 1]['value'] : $this->sinDatos;

                    // Calcular métricas solo si hay valores válidos
                    if (!empty($values)) {
                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'min_value' => min($values),
                            'max_value' => max($values),
                            'weekly_average' => array_sum($values) / count($values),
                            'current_value' => $value,
                        ];
                    } else {
                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'min_value' => $this->sinDatos,
                            'max_value' => $this->sinDatos,
                            'weekly_average' => $this->sinDatos,
                            'current_valuevalue' => $this->sinDatos,
                        ];
                    }
                } else {
                    $datalist[] = [
                        'feed_key' => $sensor->tipo_sensor,
                        'nombre_amigable' => $sensor->nombre_amigable,
                        'unidad' => $sensor->unidad,
                        'error' => 'No se pudo obtener el dato',
                    ];
                }
            } catch (\Exception $e) {
                $datalist[] = [
                    'feed_key' => $sensor->tipo_sensor,
                    'nombre_amigable' => $sensor->nombre_amigable,
                    'unidad' => $sensor->unidad,
                    'error' => 'No se pudo obtener el dato: ' . $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => 'Datos obtenidos correctamente',
            'data' => $datalist,
        ], 200);
    }

    private function obtenerDatosSensor($tipoSensor)
    {
        $sensor = Sensor::where('tipo_sensor', $tipoSensor)->first();

        if (!$sensor) {
            return response()->json([
                'message' => "Sensor con clave {$tipoSensor} no encontrado",
                'data' => null,
            ], 404);
        }

        try {
            $response = Http::withHeaders([
                'X-AIO-Key' => $this->AIOkey,
            ])->get("https://io.adafruit.com/api/v2/{$this->AIOuser}/feeds/incubadora.{$tipoSensor}/data/last");

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'feed_key' => $sensor->tipo_sensor,
                    'nombre_amigable' => $sensor->nombre_amigable,
                    'unidad' => $sensor->unidad,
                    'value' => $data['value'] ?? $this->sinDatos,
                ], 200);
            }

            return response()->json([
                'feed_key' => $sensor->tipo_sensor,
                'nombre_amigable' => $sensor->nombre_amigable,
                'unidad' => $sensor->unidad,
                'error' => 'No se pudo obtener el dato',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'feed_key' => $sensor->tipo_sensor,
                'nombre_amigable' => $sensor->nombre_amigable,
                'unidad' => $sensor->unidad,
                'error' => 'No se pudo obtener el dato: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Métodos para cada sensor
    public function bpm()
    {
        return $this->obtenerDatosSensor('bpm');
    }

    public function fotoresistencia()
    {
        return $this->obtenerDatosSensor('fotoresistencia');
    }

    public function humedad()
    {
        return $this->obtenerDatosSensor('humedad');
    }

    public function oxigeno()
    {
        return $this->obtenerDatosSensor('oxigeno');
    }

    public function rgb()
    {
        return $this->obtenerDatosSensor('rgb');
    }

    public function temperaturacorporal()
    {
        return $this->obtenerDatosSensor('temperaturacorporal');
    }

    public function temperaturambiental()
    {
        return $this->obtenerDatosSensor('temperaturambiental');
    }

    public function vibraciones()
    {
        return $this->obtenerDatosSensor('vibraciones');
    }
}
