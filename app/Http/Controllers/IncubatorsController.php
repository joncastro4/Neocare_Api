<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class IncubatorsController extends Controller
{
    public function index()
    {
        $incubators = Incubator::all();

        if (isEmpty($incubators)) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

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
        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'No Incubator Found'
            ], 404);
        }

        return response()->json([
            'data' => $incubator
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'state' => 'required|string|in:active,available,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'No Incubator Found'
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
        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $incubator->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }
}