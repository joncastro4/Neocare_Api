<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use App\Models\Cliente;

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
}