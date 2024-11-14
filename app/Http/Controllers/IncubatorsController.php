<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;

class IncubatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'state' => 'required|string|in:inactive,active',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $incubator = Incubator::create($request->all());

        if (!$incubator) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $incubator
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        return response()->json([
            'data' => $incubator
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'state' => 'nullable|string|in:inactive,active',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $incubator->state = $request->state;
        $incubator->save();

        return response()->json([
            'data' => $incubator
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
