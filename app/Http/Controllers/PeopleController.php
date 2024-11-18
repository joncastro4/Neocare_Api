<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeopleController extends Controller
{
    public function index()
    {
        $people = Person::all();

        if (!$people) {
            return response()->json([
                'msg' => "There are no people registered"
            ], 204);
        }

        return response()->json([
            'people' => $people
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'last_name_1' => 'required|string',
            'last_name_2' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $person = Person::create($request->all());

        if (!$person) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'person' => $person
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $person = Person::find($id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }

        return response()->json([
            'person' => $person
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'last_name_1' => 'nullable|string',
            'last_name_2' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $person = Person::find($id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }

        $person->name = $request->name ?? $person->name;
        $person->last_name_1 = $request->last_name_1 ?? $person->last_name_1;
        $person->last_name_2 = $request->last_name_2 ?? $person->last_name_2;
        $person->save();

        return response()->json([
            'person' => $person
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $person = Person::find($id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }

        $person->delete();

        return response()->json([
            'msg' => "Person deleted"
        ], 200);
    }
}
