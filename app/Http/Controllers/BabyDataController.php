<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby_Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Baby_Data::all();

        if (!$data)
        {
            return response()->json([
                'msg' => "There is no data registered"
            ], 204);
        }

        return response()->json([
            'data' => $data
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
            'baby_incubator_id' => 'required|integer|exists:baby_incubators,id',
            'oxygen' => 'required|integer|between:0,100',
            'heart_rate' => 'required|integer|between:0,255',
            'temperature' => 'required|decimal|between:0,100',
        ]);

        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $data = Baby_Data::create($request->all());

        if (!$data)
        {
            return response()->json([
                'msg' => "Data not registered"
            ], 400);
        }

        return response()->json([
            'data' => $data
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
        $data = Baby_Data::find($id);

        if (!$data)
        {
            return response()->json([
                'msg' => "Data not found"
            ], 404);
        }

        return response()->json([
            'data' => $data
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
            'oxygen' => 'nullable|integer|between:0,100',
            'heart_rate' => 'nullable|integer|between:0,255',
            'temperature' => 'nullable|decimal|between:0,100',
        ]);
        
        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $data = Baby_Data::find($id);

        if (!$data)
        {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Baby_Data::find($id);

        if (!$data)
        {
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
