<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use App\Models\Cliente;

class DataController extends Controller
{
    // Listar clientes
    public function index()
    {
        return response()->json(Data::all(), 200);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        $cliente = Data::create($request->all());
        return response()->json($cliente, 201);
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

    // Actualizar un cliente
    public function update(Request $request, $id)
    {
        $cliente = Data::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $cliente->update($request->all());
        return response()->json($cliente, 200);
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        $cliente = Data::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado'], 200);
    }
}