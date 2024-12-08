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
                    $data = $response->json(); // Datos históricos en formato JSON
                    $valuesByDate = [];
                    $values = [];
                    $eventosHoy = 0;
                    $eventosUltimaHora = 0;
                    $ultimoEvento = null;

                    // Filtrar y agrupar datos por fecha
                    foreach ($data as $entry) {
                        if (isset($entry['value']) && is_numeric($entry['value']) && isset($entry['created_at'])) {
                            $value = (float) $entry['value'];
                            $createdAt = new \DateTime($entry['created_at']);
                            $date = $createdAt->format('Y-m-d');
                            $valuesByDate[$date][] = $value;
                            $values[] = $value;

                            // Contar eventos de hoy
                            if ($createdAt->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {
                                $eventosHoy++;
                            }

                            // Contar eventos de la última hora
                            $ultimaHora = (new \DateTime())->modify('-1 hour');
                            if ($createdAt >= $ultimaHora) {
                                $eventosUltimaHora++;
                            }


                            $ultimoEvento = $entry;
                        }
                    }

                    $lastDate = array_key_last($valuesByDate);
                    $dailyValues = $lastDate ? $valuesByDate[$lastDate] : [];

                    $value = !empty($data) ? $data[count($data) - 1]['value'] : $this->sinDatos;

                    // Calcular métricas solo si hay valores válidos
                    if (!empty($dailyValues)) {
                        $minValue = min($dailyValues); // Mínimo del último día
                        $maxValue = max($dailyValues); // Máximo del último día

                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'min_value' => $minValue,
                            'max_value' => $maxValue,
                            'weekly_average' => array_sum($values) / count($values),
                            'current_value' => $value,
                            'eventos_hoy' => $eventosHoy,
                            'eventos_ultima_hora' => $eventosUltimaHora,
                            'ultimo_evento' => $ultimoEvento,
                        ];
                    } else {
                        $datalist[] = [
                            'feed_key' => $sensor->tipo_sensor,
                            'nombre_amigable' => $sensor->nombre_amigable,
                            'unidad' => $sensor->unidad,
                            'min_value' => $this->sinDatos,
                            'max_value' => $this->sinDatos,
                            'weekly_average' => $this->sinDatos,
                            'current_value' => $this->sinDatos,
                            'eventos_hoy' => $this->sinDatos,
                            'eventos_ultima_hora' => $this->sinDatos,
                            'ultimo_evento' => $this->sinDatos,
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
