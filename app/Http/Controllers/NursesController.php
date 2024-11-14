<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nurse;
use Illuminate\Support\Facades\Validator;

class NursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nurses = Nurse::all();

        if (!$nurses) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'data' => $nurses
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
