<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = Address::orderByDesc('created_at')->get();

        if ($addresses->isEmpty()) {
            return response()->json([
                'msg' => 'No addresses found'
            ], 404);
        }

        return response()->json([
            'addresses' => $addresses
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
            'street' => 'required|string|max:255',
            'number' => 'required|integer|min:0',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|integer|digits:5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $address = new Address();

        $address->street = $request->street;
        $address->number = $request->number;
        $address->neighborhood = $request->neighborhood;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->zip_code = $request->zip_code;
        $address->save();

        return response()->json([
            'msg' => 'Address registered successfully',
            'address' => $address
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
        $address = Address::find($id);

        if (!$address) {
            return response()->json([
                'msg' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'address' => $address
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
        $address = Address::find($id);

        if (!$address) {
            return response()->json([
                'msg' => 'Address not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'street' => 'string|max:255',
            'number' => 'integer|min:0',
            'neighborhood' => 'string|max:255',
            'city' => 'string|max:255',
            'state' => 'string|max:255',
            'zip_code' => 'integer|digits:5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $address->update($request->all());

        return response()->json([
            'msg' => 'Address updated successfully',
            'address' => $address
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
        $address = Address::find($id);

        if (!$address) {
            return response()->json([
                'msg' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'msg' => 'Address deleted successfully'
        ]);
    }
}