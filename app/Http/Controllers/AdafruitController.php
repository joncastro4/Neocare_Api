<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdafruitController extends Controller
{
    private $AIOkey;
    private $AIOuser;

    public function __construct()
    {
        $this->AIOkey = 'aio_nBRg95EbrYiAnrK6jxq89C2bTHXH';
        $this->AIOuser = 'Tunas';
    }

    public $sinDatos = 'Sin datos disponibles';

    public function obtenerTodosLosSensores()
    {
        $sensores = Sensor::whereNot('tipo_sensor', 'rgb')->get();
        $datalist = [];
    
        foreach ($sensores as $sensor) {
            try {
                $response = Http::withHeaders([
                    'X-AIO-Key' => $this->AIOkey,
                ])->get("https://io.adafruit.com/api/v2/{$this->AIOuser}/feeds/pruebas.{$sensor->tipo_sensor}/data");
    
                if ($response->successful()) {
                    $data = $response->json();
                    $values = [];
                    $eventosHoy = 0;
                    $eventosUltimaHora = 0;
                    $ultimoEvento = null;
                    $values24Hours = [];  // Array para los valores de las últimas 24 horas
    
                    $currentTime = now();
                    foreach ($data as $entry) {
                        if (isset($entry['value']) && is_numeric($entry['value'])) {
                            $value = (float) $entry['value'];
                            $values[] = $value;
    
                            // Filtrar valores de las últimas 24 horas
                            $timestamp = Carbon::parse($entry['created_at']);
                            if ($timestamp->diffInHours($currentTime) <= 24) {
                                $values24Hours[] = $value;
                            }
    
                            if (in_array($sensor->tipo_sensor, ['movimiento', 'vibracion'])) {
                                if ($value === 1.0) {
                                    // Contar eventos de hoy
                                    $eventosHoy += $timestamp->isToday() ? 1 : 0;
    
                                    // Contar eventos en la última hora
                                    $eventosUltimaHora += $timestamp->diffInHours($currentTime) < 1 ? 1 : 0;
    
                                    // Actualizar último evento
                                    $ultimoEvento = $ultimoEvento ? max($ultimoEvento, $timestamp) : $timestamp;
                                }
                            }
                        }
                    }
    
                    $currentValue = !empty($data) ? $data[0]['value'] : $this->sinDatos;
    
                    if (in_array($sensor->tipo_sensor, ['movimiento', 'vibracion'])) {
                        // Cálculo del porcentaje de actividad diaria
                        $maxEventosPosibles = 288; // Supongamos 288 posibles eventos en un día
                        $porcentajeActividad = ($eventosHoy / $maxEventosPosibles) * 100;
    
                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'current_value' => $currentValue,
                            'eventos_hoy' => $eventosHoy,
                            'eventos_ultima_hora' => $eventosUltimaHora,
                            'ultimo_evento' => $ultimoEvento ? $ultimoEvento->diffForHumans() : 'Sin eventos',
                            'porcentaje_actividad' => round($porcentajeActividad, 2) . '%',
                        ];
                    } else {
                        $minValue = !empty($values) ? min($values) : $this->sinDatos;
                        $maxValue = !empty($values) ? max($values) : $this->sinDatos;
    
                        // Calcular min y max de las últimas 24 horas
                        $minValue24Hours = !empty($values24Hours) ? min($values24Hours) : $this->sinDatos;
                        $maxValue24Hours = !empty($values24Hours) ? max($values24Hours) : $this->sinDatos;
    
                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'min_value' => $minValue,
                            'max_value' => $maxValue,
                            'min_value_24h' => $minValue24Hours,  // Valor mínimo en las últimas 24 horas
                            'max_value_24h' => $maxValue24Hours,  // Valor máximo en las últimas 24 horas
                            'weekly_average' => !empty($values) ? (array_sum($values) / count($values)) : $this->sinDatos,
                            'current_value' => $currentValue,
                        ];
                    }
                } else {
                    $datalist[] = $this->datosSinConexion($sensor);
                }
            } catch (\Exception $e) {
                $datalist[] = $this->datosError($sensor, $e);
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
