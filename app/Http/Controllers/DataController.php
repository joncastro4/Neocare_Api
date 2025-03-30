<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function index()
    {
        $allData = Data::all();
        $keys = ['HAM', 'LDR', 'PRE', 'SON', 'TAM', 'TBB', 'VRB'];
        $accumulators = [];
    
        foreach ($allData as $data) {
            foreach ($keys as $key) {
                if (!isset($data->$key)) {
                    continue;
                }
                
                $valueData = $data->$key;
                
                if (!isset($valueData['value'])) {
                    continue;
                }
                
                $value = (float)$valueData['value'];
                
                if (!isset($accumulators[$key])) {
                    $accumulators[$key] = [];
                }
                
                $accumulators[$key][] = $value;
            }
        }
    
        $result = [];
        
        foreach ($accumulators as $key => $values) {
            $count = count($values);
            
            if ($count === 0) {
                continue;
            }
            
            $sum = array_sum($values);
            $average = $sum / $count;
            
            $result[$key] = [
                'promedio' => round($average, 2),
                'maximo' => max($values),
                'minimo' => min($values),
            ];
        }
    
        return response()->json($result, 200);
    }


    public function show($id)
    {
        $cliente = Data::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente, 200);
    }

    public function getByType(Request $request, $type)
    {
    
        $sensors = Data::where('sensor.type', $type)->get();

        if ($sensors->isEmpty()) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json($sensors, 200);
    }

    public function getByIncubatorId($incubator_id)
    {
        // Buscar el documento donde el incubator_id coincida
        $documento = Data::where('incubator_id', (int) $incubator_id)->first();
    
        // Verificar si se encontró el documento
        if (!$documento) {
            return response()->json(['message' => 'No se encontró el documento'], 404);
        }
    
        // Convertir los valores a un array estándar de PHP
        $valuesArray = $documento->getAttributes()['values'] ?? [];
        if (is_object($valuesArray)) {
            $valuesArray = json_decode(json_encode($valuesArray), true);
        }
    
        // Procesar los datos para obtener estadísticas
        $sensorStats = [];
        $sensors = ['HAM', 'LDR', 'PRE', 'SON', 'TAM', 'TBB', 'VRB']; // Lista de sensores
    
        foreach ($sensors as $sensor) {
            $values = [];
            $dates = [];
            
            // Recoger todos los valores y fechas para este sensor
            foreach ($valuesArray as $entry) {
                if (isset($entry[$sensor])) {
                    $values[] = floatval($entry[$sensor]['value']);
                    $dates[] = $entry[$sensor]['date'];
                }
            }
    
            if (!empty($values)) {
                // Calcular estadísticas
                $sensorStats[$sensor] = [
                    'average' => round(array_sum($values) / count($values), 2),
                    'min' => round(min($values), 2),
                    'max' => round(max($values), 2),
                    'count' => count($values),
                    'first_reading' => $dates[0],
                    'last_reading' => end($dates),
                    'range' => round(max($values) - min($values), 2),
                    'unit' => $this->getSensorUnit($sensor)
                ];
            } else {
                $sensorStats[$sensor] = [
                    'message' => 'No hay datos para este sensor'
                ];
            }
        }
    
        // Respuesta con los datos originales y las estadísticas
        return response()->json([
            'incubator_id' => $documento->incubator_id,
            'total_readings' => count($valuesArray),
            'first_reading_time' => $valuesArray[0]['HAM']['date'] ?? null,
            'last_reading_time' => end($valuesArray)['HAM']['date'] ?? null,
            'sensor_statistics' => $sensorStats,
            // 'raw_data' => $documento // Opcional: comentado para evitar posibles problemas
        ]);
    }
    
    private function getSensorUnit($sensor)
    {
        $units = [
            'HAM' => '%',    // Humedad relativa
            'TAM' => '°C',    // Temperatura ambiente
            'TBB' => '°C',    // Temperatura bebé
            'LDR' => 'lux',   // Intensidad luminosa
            'SON' => 'Hz',    // Sonido
            'PRE' => 'Pa',     // Presión
            'VRB' => 'm/s'    // Vibración
        ];
        
        return $units[$sensor] ?? 'unidad desconocida';
    }

    public function getLatestByIncubatorId(Request $request, $incubator_id)
    {
        $validator = Validator::make($request->all(), [
            'sensor' => 'nullable|string|in:TAM,HAM,TBB,LDR,SON,VRB,PRE'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $documento = Data::where('incubator_id', (int) $incubator_id)->first();

        if (!$documento) {
            return response()->json([
                'message' => 'No se encontraron datos.'
            ], 404);
        }

        if ($request->has('sensor')) {
            $sensor = $request->sensor;

            $history = collect($documento->values)
                ->pluck($sensor)
                ->filter()
                ->map(function ($data) {
                    return [
                        'value' => $data['value'],
                        'date' => $data['date']
                    ];
                })
                ->sortBy('date')
                ->values();

            if ($history->isEmpty()) {
                return response()->json([
                    'message' => 'No hay datos para el sensor especificado.'
                ], 404);
            }

            return response()->json($history, 200);
        }

        $latestValues = collect($documento->values)
            ->sortByDesc(function ($item) {
                return collect($item)->first()['date'];
            })->first();

        return response()->json($latestValues, 200);
    }
}