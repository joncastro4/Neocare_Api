<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relative;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;

class RelativesController extends Controller
{
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
            ], 400);
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

        return response()->json([
            'relative' => $relative
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
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
}
