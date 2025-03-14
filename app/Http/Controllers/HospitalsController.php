<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Incubator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Baby;
use App\Models\Nurse;
use App\Models\Room;

class HospitalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::with('address')->orderByDesc('created_at')->paginate(8);

        $hospitals->getCollection()->transform(function ($hospital) {
            return [
                'id' => $hospital->id,
                'name' => $hospital->name,
                'phone_number' => $hospital->phone_number,
                'city' => $hospital->address->city,
                'created_at' => $hospital->created_at->format('Y-m-d'),
            ];
        });

        if ($hospitals->isEmpty()) {
            return response()->json([
                'message' => "No hospitals found"
            ], 404);
        }

        return response()->json([
            'message' => "Hospitals found",
            'hospitals' => $hospitals
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
            'address_id' => 'required|integer|exists:addresses,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|size:10',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors()
            ], 400);
        }

        $hospital = new Hospital();

        $hospital->address_id = $request->address_id;
        $hospital->name = $request->name;
        $hospital->phone_number = $request->phone_number;
        $hospital->save();

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital not registered'
            ], 400);
        }

        return response()->json([
            'message' => 'Hospital registered successfully',
            'hospital' => $hospital
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
        $hospital = Hospital::with('address')->find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital not found'
            ], 404);
        }

        $hospital->total_incubators = Incubator::whereHas('room', function ($query) use ($id) {
            $query->where('hospital_id', $id);
        })->count();

        $hospital->total_babies = Baby::where('hospital_id', $id)->count();

        $hospital->total_nurses = Nurse::where('hospital_id', $id)->count();

        $hospital->total_rooms = Room::where('hospital_id', $id)->count();



        return response()->json([
            'hospital' => [
                'id' => $hospital->id,
                'name' => $hospital->name,
                'phone_number' => $hospital->phone_number,
                'created_at' => $hospital->created_at->format('Y-m-d'),
                'total_incubators' => $hospital->total_incubators,
                'total_babies' => $hospital->total_babies,
                'total_nurses' => $hospital->total_nurses,
                'total_rooms' => $hospital->total_rooms,
                'address' => $hospital->address
            ],
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
        $hospital = Hospital::find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'address_id' => 'required|integer|exists:addresses,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $hospital->update($request->all());

        return response()->json([
            'message' => 'Hospital updated successfully',
            'hospital' => $hospital
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hospital = Hospital::find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital not found'
            ], 404);
        }

        $hospital->delete();

        return response()->json([
            'message' => 'Hospital deleted successfully'
        ]);
    }
}