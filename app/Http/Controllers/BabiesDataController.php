<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\BabyData;

class BabiesDataController extends Controller
{
    public $dataNotFound = "Data not found";


    public function index()
    {
        $data = BabyData::all();

        if (!$data) {
            return response()->json([
                'msg' => "There is no data registered"
            ], 204);
        }

        return response()->json([
            'data' => $data
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'baby_incubator_id' => 'required|integer|exists:baby_incubators,id',
            'oxygen' => 'required|integer|between:0,100',
            'heart_rate' => 'required|integer|between:0,255',
            'temperature' => 'required|decimal|between:0,100',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data = BabyData::create($request->all());

        if (!$data) {
            return response()->json([
                'msg' => "Data not registered"
            ], 400);
        }

        return response()->json([
            'data' => $data
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        // Cargar BabyData
        $data = BabyData::find($id);

        if (!$data) {
            return response()->json([
                'msg' => $this->dataNotFound
            ], 404);
        }

        $egressDate = $data->baby_incubator->baby->egress_date ?? null;
        $name = $data->baby_incubator->baby->person->name ?? null;
        $last_name_1 = $data->baby_incubator->baby->person->last_name_1 ?? null;
        $last_name_2 = $data->baby_incubator->baby->person->last_name_2 ?? null;
        $baby = $name . ' ' . $last_name_1 . ' ' . $last_name_2;
        $state = $data->baby_incubator->incubator->state ?? null;
        $baby_incubator_id = $data->baby_incubator->id ?? null;

        return response()->json([
            "message" => 'Datos obtenidos correctamente',
            "data" => [
                "egress_date" => $egressDate,
                "baby" => $baby,
                "state" => $state,
                "baby_incubator_id" => $baby_incubator_id
            ],
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'oxygen' => 'nullable|integer|between:0,100',
            'heart_rate' => 'nullable|integer|between:0,255',
            'temperature' => 'nullable|decimal|between:0,100',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $data = BabyData::find($id);

        if (!$data) {
            return response()->json([
                'msg' => $this->dataNotFound
            ], 404);
        }

        $data->oxygen = $request->oxygen ?? $data->oxygen;
        $data->heart_rate = $request->heart_rate ?? $data->heart_rate;
        $data->temperature = $request->temperature ?? $data->temperature;
        $data->save();

        return response()->json([
            'data' => $data
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $data = BabyData::find($id);

        if (!$data) {
            return response()->json([
                'msg' => $this->dataNotFound
            ], 404);
        }

        $data->delete();

        return response()->json([
            'msg' => "Data deleted"
        ], 200);
    }
}