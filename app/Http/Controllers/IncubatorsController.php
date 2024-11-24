<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyIncubator;
use App\Models\NurseBaby;
use Auth;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;

use App\Models\Nurse;

class IncubatorsController extends Controller
{
    public $incubatorNotFound = 'No Incubator Found';

    public function index()
    {
        $incubators = Incubator::all();

        if (!$incubators) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'data' => $incubators
        ], 200);
    }
    public function incubatorNurse()
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json([
                'msg' => 'No user found'
            ], 404);
        }
    
        // Obtén los registros NurseBaby asociados al usuario (enfermera)
        $babyNurses = NurseBaby::where('nurse_id', $user->id)->get();
    
        if ($babyNurses->isEmpty()) {
            return response()->json([
                'msg' => 'No baby nurses found for this user'
            ], 404);
        }
    
        // Ahora, obtenemos las incubadoras asociadas a los bebés de esa enfermera
        $incubators = BabyIncubator::whereIn('baby_id', $babyNurses->pluck('baby_id'))->get();
    
        if ($incubators->isEmpty()) {
            return response()->json([
                'msg' => 'No incubators found for these babies'
            ], 204);
        }
    
        // Retorna la respuesta con los datos de incubadoras
        return response()->json([
            'data' => $incubators
        ], 200);
    }
    
    public function store()
    {
        $incubator = new Incubator();
        $incubator->save();

        return response()->json([
            'msg' => 'Incubator Created Successfully',
            'data' => $incubator
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        return response()->json([
            'data' => $incubator
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $validate = Validator::make($request->all(), [
            'state' => 'required|string|in:active,available,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->state = $request->state;
        $incubator->save();

        return response()->json([
            'data' => $incubator
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->delete();

        return response()->json([
            'msg' => 'Incubator Deleted'
        ], 200);
    }
}