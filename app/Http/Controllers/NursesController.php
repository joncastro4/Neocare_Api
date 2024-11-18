<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nurse;
use Illuminate\Support\Facades\Validator;

class NursesController extends Controller
{
    public function index()
    {
        $nurses = Nurse::all();

        if (!$nurses) {
            return response()->json([
                'msg' => 'No Nurses Found'
            ], 204);
        }

        return response()->json([
            'data' => $nurses
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'person_id' => 'required|integer|exists:people,id|unique:nurses,person_id',
            'rfc' => 'required|string|unique:nurses,rfc',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $nurse = Nurse::create($request->all());

        if (!$nurse) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $nurse
        ], 200);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $nurse = Nurse::find($id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        return response()->json([
            'data' => $nurse
        ]);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'person_id' => 'nullable|integer|exists:people,id|unique:nurses,person_id',
            'rfc' => 'nullable|string|unique:nurses,rfc',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $nurse = Nurse::find($id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $nurse->person_id = $request->person_id ?? $nurse->person_id;
        $nurse->rfc = $request->rfc ?? $nurse->rfc;
        $nurse->save();

        return response()->json([
            'data' => $nurse
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $nurse = Nurse::find($id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $nurse->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }
}
