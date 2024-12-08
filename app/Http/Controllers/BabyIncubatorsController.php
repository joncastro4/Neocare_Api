<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use App\Models\BabyIncubator;
use App\Models\Incubator;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyIncubatorsController extends Controller
{
    public function index()
    {
        $baby_incubators = BabyIncubator::all();

        if (!$baby_incubators) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'baby_incubators' => $baby_incubators
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'baby_id' => 'required|integer|exists:babies,id',
            'incubator_id' => 'required|integer|exists:incubators,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $baby_incubator = BabyIncubator::create($request->all());

        if (!$baby_incubator) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'baby_incubator' => $baby_incubator
        ], 200);
    }
    public function show($id)
    {
        // Verificar que el ID es numérico
        if (!is_numeric($id)) {
            return response()->json([
                'msg' => 'ID inválido'
            ], 404);
        }
    
        // Buscar el incubator relacionado
        $incubator = Incubator::find($id);
    
        if (!$incubator) {
            return response()->json([
                'msg' => 'Incubadora no encontrada'
            ], 404);
        }
    
        // Buscar la relación en BabyIncubator usando el incubator_id
        $babyIncubator = BabyIncubator::where('incubator_id', $incubator->id)->first();
    
        if (!$babyIncubator) {
            return response()->json([
                'msg' => 'Relación entre bebé e incubadora no encontrada'
            ], 404);
        }
    
        // Buscar el bebé relacionado
        $baby = Baby::find($babyIncubator->baby_id);
    
        if (!$baby) {
            return response()->json([
                'msg' => 'Bebé no encontrado'
            ], 404);
        }
    
        // Buscar la persona asociada al bebé
        $person = Person::find($baby->person_id);
    
        if (!$person) {
            return response()->json([
                'msg' => 'Persona no encontrada'
            ], 404);
        }
    
        // Preparar los datos
        $egressDate = $baby->egress_date ?? null;
        $name = $person->name ?? null;
        $lastName1 = $person->last_name_1 ?? null;
        $lastName2 = $person->last_name_2 ?? null;
        $babyName = trim("{$name} {$lastName1} {$lastName2}");
        $state = $incubator->state ?? null;
    
        // Retornar los datos en la respuesta
        return response()->json([
            "message" => 'Datos obtenidos correctamente',
            "data" => [
                "egress_date" => $egressDate,
                "baby" => $babyName,
                "state" => $state,
                "baby_incubator_id" => $babyIncubator->id
            ],
        ], 200);
    }    
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'baby_id' => 'nullable|integer|exists:babies,id',
            'incubator_id' => 'nullable|integer|exists:incubators,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $baby_incubator = BabyIncubator::find($id);

        if (!$baby_incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $baby_incubator->baby_id = $request->baby_id ?? $baby_incubator->baby_id;
        $baby_incubator->incubator_id = $request->incubator_id ?? $baby_incubator->incubator_id;
        $baby_incubator->save();

        return response()->json([
            'baby_incubator' => $baby_incubator
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby_incubator = BabyIncubator::find($id);

        if (!$baby_incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $baby_incubator->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }
}
