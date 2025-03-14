<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressesController extends Controller
{
    public function index()
    {
        $addresses = Address::orderByDesc('created_at')->paginate(9);

        if ($addresses->isEmpty()) {
            return response()->json([
                'msg' => 'No addresses found'
            ], 404);
        }

        return response()->json([
            'addresses' => $addresses
        ]);
    }
    public function indexNoPaginate()
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
            return response()->json($validate->errors(), 400);
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
    public function update(Request $request, $id)
    {
        $address = Address::find($id);

        if (!$address) {
            return response()->json([
                'msg' => 'Address not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'street' => 'required|string|max:255',
            'number' => 'required|integer|min:0',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|integer|digits:5'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $address->update($request->all());

        return response()->json([
            'msg' => 'Address updated successfully',
            'address' => $address
        ]);
    }
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