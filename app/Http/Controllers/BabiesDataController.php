<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabiesDataController extends Controller
{
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
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
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
        $data = BabyData::find($id);

        if (!$data) {
            return response()->json([
                'msg' => "Data not found"
            ], 404);
        }

        return response()->json([
            'data' => $data
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
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $data = BabyData::find($id);

        if (!$data) {
            return response()->json([
                'msg' => "Data not found"
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
                'msg' => "Data not found"
            ], 404);
        }

        $data->delete();

        return response()->json([
            'msg' => "Data deleted"
        ], 200);
    }
}
