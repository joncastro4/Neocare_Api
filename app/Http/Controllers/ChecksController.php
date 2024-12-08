<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Check;
use App\Models\Nurse;
use Illuminate\Support\Facades\Validator;

class ChecksController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'Unauthorized'
            ]);
        }


        // Si el usuario no es admin, obtenemos los BabyNurses asociados
        if ($user->role != 'admin') {
            $checks = Check::where('nurse_id', $user->id)->get();

            if ($checks->isEmpty()) {
                return response()->json([
                    'msg' => 'No hay chequeos para esta enfermera'
                ], 404);
            }


            return response()->json([
                'data' => $checks
            ]);
        } else {
            // Si el usuario es admin, obtenemos todas las incubadoras sin filtro
            $checks = Check::all();
        }

        return response()->json([
            'data' => $checks
        ], 200);
    }
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }

        $user_id = $user->id;

        $nurse = Nurse::where('user_id', $user_id)->first();

        if (!$nurse) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }

        $nurse_id = $nurse->id;

        if (!$nurse_id) {
            return response()->json([
                'msg' => 'No Nurse Found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'baby_incubator_id' => 'required|integer|exists:babies_incubators,id',
            'description' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $check = Check::create([
            'nurse_id' => $nurse_id,
            'baby_incubator_id' => $request->baby_incubator_id,
            'description' => $request->description
        ]);

        if (!$check) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'msg' => 'Chequeo registrado exitosamente',
            'data' => $check
        ], 200);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Check Found'
            ], 404);
        }

        return response()->json([
            'data' => $check
        ]);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'description' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Check Found'
            ], 404);
        }

        $check->description = $request->description;
        $check->save();

        return response()->json([
            'data' => $check
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $check->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }

    public function checksByNurse()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'unauthorized'
            ], 403);
        }

        // Obtener las IDs de las enfermeras asociadas al usuario
        $nurseIds = Nurse::where('user_id', $user->id)->orderByDesc('created_at')->pluck('id');

        if ($nurseIds->isEmpty()) {
            return response()->json([
                'msg' => 'unauthorized'
            ], 403);
        }

        // Obtener los chequeos asociados, ordenados de manera descendiente por created_at
        $checks = Check::whereIn('nurse_id', $nurseIds)
            ->with(['baby_incubator.baby.person'])
            ->orderByDesc('created_at') // Orden descendente
            ->get();

        // Mapear los resultados
        $data = $checks->map(function ($check) {
            $babyIncubator = $check->baby_incubator;
            $baby = $babyIncubator ? $babyIncubator->baby : null;
            $person = $baby ? $baby->person : null;

            $createdAt = $check->created_at;
            $date = $createdAt->format('Y-m-d');
            $time = $createdAt->format('H:i:s');

            return [
                'check_id' => $check->id,
                'description' => $check->description,
                'date' => $date,
                'time' => $time,
                'baby' => $person ? $person->name . ' ' . $person->last_name_1 . ' ' . $person->last_name_2 : null
            ];
        });

        // Retornar los chequeos
        return response()->json([
            'checks' => $data
        ], 200);
    }
}