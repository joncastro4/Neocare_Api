<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nurse_Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyNursesController extends Controller
{
    public function index()
    {
        $data = Nurse_Baby::all();

        if (!$data) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'data' => $data
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nurse_id' => 'required|integer|exists:nurses,id',
            'baby_id' => 'required|integer|exists:babies,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $data = Nurse_Baby::create($request->all());

        if (!$data) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $data
        ], 200);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $data = Nurse_Baby::find($id);

        if (!$data) {
            return response()->json([
                'msg' => 'No Data Found'
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
            'nurse_id' => 'nullable|integer|exists:nurses,id',
            'baby_id' => 'nullable|integer|exists:babies,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $data = Nurse_Baby::find($id);

        if (!$data) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $data->nurse_id = $request->nurse_id ?? $data->nurse_id;
        $data->baby_id = $request->baby_id ?? $data->baby_id;
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
        $data = Nurse_Baby::find($id);

        if (!$data) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }
}
