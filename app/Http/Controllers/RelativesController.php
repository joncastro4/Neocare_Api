<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use App\Models\Relative;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\User;

class RelativesController extends Controller
{
    public function index()
    {
        $relatives = Relative::with('person')->get();

        if ($relatives->isEmpty()) {
            return response()->json([
                'msg' => 'No Relatives Found'
            ], 204);
        }

        return response()->json([
            'msg' => 'Relatives Found Successfully',
            'relatives' => $relatives
        ]);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'baby_id' => 'required|integer|exists:babies,id',
            'phone_number' => 'required|string|min:10|max:10',
            'contact' => 'nullable|string|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $person = new Person();
        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;
        $person->save();

        if (!$person) {
            return response()->json([
                'msg' => 'Person not registered'
            ], 400);
        }

        $relative = new Relative();

        $relative->person_id = $person->id;
        $relative->baby_id = $request->baby_id;
        $relative->phone_number = $request->phone_number;
        $relative->contact = $request->contact;
        $relative->save();

        if (!$relative) {
            return response()->json([
                'msg' => 'Relative not registered'
            ], 400);
        }

        return response()->json([
            'msg' => 'Relative registered Successfully',
            'person' => $person,
            'relative' => $relative
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $person = Person::find($relative->person_id);

        if (!$person) {
            return response()->json([
                'msg' => 'No Person Found'
            ], 404);
        }

        $relativeData = $person->relative->map(function ($relative) {
            return [
                'name' => $relative->contact,
                'phone_number' => $relative->phone_number,
                'contact' => $relative->contact,
            ];
        })->first();

        return response()->json([
            'msg' => 'Relative Found Successfully',
            'person' => [
                'id' => $person->id,
                'name' => $person->name,
                'last_name_1' => $person->last_name_1,
                'last_name_2' => $person->last_name_2,
                'phone_number' => $relative->phone_number,
                'contact' => $relative->contact,
            ]
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'string|max:255|nullable',
            'last_name_1' => 'string|max:255|nullable',
            'last_name_2' => 'string|max:255|nullable',
            'baby_id' => 'integer|exists:babies,id|nullable',
            'phone_number' => 'string|min:10|max:10|nullable',
            'contact' => 'string|nullable',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Data Found'
            ], 404);
        }

        $person = Person::find($relative->person_id);

        if (!$person) {
            return response()->json([
                'msg' => 'No Person Found'
            ], 404);
        }

        $person->name = $request->name ?? $person->name;
        $person->last_name_1 = $request->last_name_1 ?? $person->last_name_1;
        $person->last_name_2 = $request->last_name_2 ?? $person->last_name_2;
        $person->save();

        $relative->baby_id = $request->baby_id ?? $relative->baby_id;
        $relative->phone_number = $request->phone_number ?? $relative->phone_number;
        $relative->contact = $request->contact ?? $relative->contact;
        $relative->save();

        return response()->json([
            'msg' => 'Relative Updated Successfully',
            'person' => $person,
            'relative' => $relative
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $relative = Relative::find($id);

        if (!$relative) {
            return response()->json([
                'msg' => 'No Relative Found'
            ], 404);
        }

        $person = Person::find($relative->person_id);

        if (!$person) {
            return response()->json([
                'msg' => 'No Person Found'
            ], 404);
        }

        $person->delete();

        $relative->delete();

        return response()->json([
            'msg' => 'Data Deleted'
        ], 200);
    }

    public function addPersonRelative (Request $request, $baby_id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'string|max:255|required',
            'last_name_1' => 'string|max:255|required',
            'last_name_2' => 'string|max:255',
            'phone_number' => 'string|min:10|max:10|required',
            'contact' => 'string|required',
        ]);

        if ($validate->fails()) 
        {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        if (Relative::where('phone_number', $request->phone_number)->first()) 
        {
            return response()->json([
                'msg' => 'Phone Number Already Registered'
            ], 400);
        }

        if (Baby::find($baby_id) == null) 
        {
            return response()->json([
                'msg' => 'Baby Not Found'
            ], 404);
        }

        $person = new Person();
        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;
        $person->save();

        $relative = new Relative();
        $relative->person_id = $person->id;
        $relative->baby_id = $baby_id;
        $relative->phone_number = $request->phone_number;
        $relative->contact = $request->contact;
        $relative->save();

        return response()->json([
            'msg' => 'Relative Added Successfully',
            'person' => $person,
            'relative' => $relative
        ], 201);
    }
}
