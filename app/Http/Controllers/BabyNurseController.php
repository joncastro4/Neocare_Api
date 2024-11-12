<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nurse_Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyNurseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Nurse_Baby::all();

        if (!$data)
        {
            return response()->json([
                'msg' => 'No Data Found'
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
            'nurse_id' => 'required|integer|exists:nurses,id',
            'baby_id' => 'required|integer|exists:babies,id',
        ]);

        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $data = Nurse_Baby::create($request->all());

        if (!$data)
        {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $data
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
        $data = Nurse_Baby::find($id);

        if (!$data)
        {
            return response()->json([
                'msg' => 'No Data Found'
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
            'nurse_id' => 'nullable|integer|exists:nurses,id',
            'baby_id' => 'nullable|integer|exists:babies,id',
        ]);

        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $data = Nurse_Baby::find($id);

        if (!$data)
        {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Nurse_Baby::find($id);

        if (!$data)
        {
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
