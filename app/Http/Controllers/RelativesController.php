<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relative;
use Illuminate\Support\Facades\Validator;

class RelativesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $relatives = Relative::all();

        if (!$relatives) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 204);
        }

        return response()->json([
            'relatives' => $relatives
        ]);
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
            'baby_id' => 'required|integer|exists:babies,id',
            'phone_number' => 'required|string|min:10|max:10',
            'contact' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $relative = Relative::create($request->all());

        if (!$relative) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'relative' => $relative
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
        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        return response()->json([
            'relative' => $relative
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
            'person_id' => 'integer|exists:people,id|nullable',
            'baby_id' => 'integer|exists:babies,id|nullable',
            'phone_number' => 'string|min:10|max:10|nullable',
            'contact' => 'string|nullable',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $relative->person_id = $request->person_id ?? $relative->person_id;
        $relative->baby_id = $request->baby_id ?? $relative->baby_id;
        $relative->phone_number = $request->phone_number ?? $relative->phone_number;
        $relative->contact = $request->contact ?? $relative->contact;
        $relative->save();

        return response()->json([
            'relative' => $relative
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
        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $relative->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }
}
