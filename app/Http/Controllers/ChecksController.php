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
        $checks = Check::with('nurse', 'baby')->all();

        if (!$checks) {
            return response()->json([
                'msg' => 'No Checks Found'
            ], 204);
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
            'msg' => 'Data Registered Successfully',
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
}
