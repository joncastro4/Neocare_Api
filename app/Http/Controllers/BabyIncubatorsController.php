<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby_Incubator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyIncubatorsController extends Controller
{
    public function index()
    {
        $baby_incubators = Baby_Incubator::all();

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

        $baby_incubator = Baby_Incubator::create($request->all());

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
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby_incubator = Baby_Incubator::find($id);

        if (!$baby_incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        return response()->json([
            'baby_incubator' => $baby_incubator
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

        $baby_incubator = Baby_Incubator::find($id);

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
        $baby_incubator = Baby_Incubator::find($id);

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
