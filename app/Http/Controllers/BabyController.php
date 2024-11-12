<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BabyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $babies = Baby::all();

        if (!$babies)
        {
            return response()->json([
                'msg' => "There are no babies registered"
            ], 204);
        }

        return response()->json([
            'babies' => $babies
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
            'person_id' => 'required|integer|exists:people,id',
            'date_of_birth' => 'required|date',
            'ingress_date' => 'required|date|after_or_equal:date_of_birth',
            'egress_date' => 'required|date|after_or_equal:ingress_date|nullable',
        ]);

        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $baby = Baby::create($request->all());

        if (!$baby)
        {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'baby' => $baby
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
        $baby = Baby::find($id);

        if (!$baby)
        {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        return response()->json([
            'baby' => $baby
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
            'date_of_birth' => 'nullable|date',
            'ingress_date' => 'nullable|date|after_or_equal:date_of_birth',
            'egress_date' => 'nullable|date|after_or_equal:ingress_date|nullable',
        ]);

        if ($validate->fails())
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $baby = Baby::find($id);

        if (!$baby)
        {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $baby->date_of_birth = $request->date_of_birth ?? $baby->date_of_birth;
        $baby->ingress_date = $request->ingress_date ?? $baby->ingress_date;
        $baby->egress_date = $request->egress_date ?? $baby->egress_date;
        $baby->save();

        return response()->json([
            'baby' => $baby
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
        $baby = Baby::find($id);

        if (!$baby)
        {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $baby->delete();

        return response()->json([
            'msg' => "Baby deleted"
        ], 200);
    }
}
