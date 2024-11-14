<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Check;
use Illuminate\Support\Facades\Validator;

class ChecksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checks = Check::all();

        if (!$checks) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'data' => $checks
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
            'description' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $check = Check::create($request->all());

        if (!$check) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $check
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
        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        return response()->json([
            'data' => $check
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
            'description' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $check->description = $request->description;
        $check->save();

        return response()->json([
            'data' => $check
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
