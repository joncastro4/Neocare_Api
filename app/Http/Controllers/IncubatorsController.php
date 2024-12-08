<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyIncubator;
use App\Models\NurseBaby;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;

use App\Models\Nurse;
use Illuminate\Support\Facades\Http;

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
    
        // Si el usuario no es admin, obtenemos los BabyNurses asociados
        if ($user->role != 'admin') {
            $babyNurses = NurseBaby::where('nurse_id', $user->id)->get();
    
            if ($babyNurses->isEmpty()) {
                return response()->json([
                    'msg' => 'No baby nurses found for this user'
                ], 404);
            }
        
            // Obtén las incubadoras asociadas a los bebés de esa enfermera
            $incubators = BabyIncubator::whereIn('baby_id', $babyNurses->pluck('baby_id'))->get();
        } else {
            // Si el usuario es admin, obtenemos todas las incubadoras sin filtro
            $incubators = BabyIncubator::all();
        }
        
        if ($incubators->isEmpty()) {
            return response()->json([
                'msg' => 'No incubators found for these babies'
            ], 204);
        }
        
        // Agrega el estado de cada incubadora al arreglo
        $incubatorsWithState = $incubators->map(function ($incubator) {
            $incubatorDetails = Incubator::find($incubator->incubator_id);
            $incubator->state = $incubatorDetails ? $incubatorDetails->state : 'Unknown';
            return $incubator;
        });
        
        return response()->json([
            'data' => $incubatorsWithState
        ], 200);
    }    

    public function store()
    {
        $incubator = new Incubator();
        $incubator->save();

        $groupName = 'incubator' . $incubator->id;
        $groupData = [
            'name' => $groupName,
            'description' => 'Incubator ' . $incubator->id,
        ];

        try 
        {
            $response = Http::withHeaders([
                'X-AIO-Key' => "aio_nBRg95EbrYiAnrK6jxq89C2bTHXH",
            ])->post("https://io.adafruit.com/api/v2/Tunas/groups", $groupData);

            if (!$response->successful()) 
            {
                return response()->json([
                    'message' => 'Error al crear el grupo.',
                    'error' => $response->json(),
                ], $response->status());
            }
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'message' => 'No se pudo crear el grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'msg' => 'Incubadora Agregada Correctamente',
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
