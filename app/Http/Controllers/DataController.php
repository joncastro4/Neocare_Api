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
        return response()->json(Data::all(), 200);
    }

    // Mostrar un cliente por ID
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
        // Busca los sensores con el tipo especificado
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

        return response()->json($documento);
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