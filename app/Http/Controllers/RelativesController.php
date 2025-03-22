<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relative;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;

class RelativesController extends Controller
{
    public function indexWithBaby($baby_id)
    {
        $relatives = Relative::with('person')
            ->where('baby_id', $baby_id)
            ->paginate(4);

        if ($relatives->isEmpty()) {
            return response()->json([
                'msg' => 'No Relatives Found for this Baby'
            ], 204);
        }

        return response()->json([
            'msg' => 'Relatives Found Successfully',
            'relatives' => $relatives
        ]);
    }

    public function index()
    {
        $relatives = Relative::with('person')->paginate(9);

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
            'email' => 'required|email|max:255|unique:relatives,email',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $person = Person::create([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
        ]);

        if (!$person) {
            return response()->json([
                'msg' => 'Person not registered'
            ], 400);
        }

        $relative = Relative::create([
            'person_id' => $person->id,
            'baby_id' => $request->baby_id,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        if (!$relative) {
            return response()->json([
                'msg' => 'Relative not registered'
            ], 400);
        }

        return response()->json([
            'msg' => 'Relative registered Successfully',
        ], 201);
    }
    // Listo
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $relative = Relative::with('person')->find($id);

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


        return response()->json([
            'msg' => 'Relative Found Successfully',
            'relative' => $relative
        ], 200);
    }
    // Listo
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'string|max:255|required',
            'last_name_1' => 'string|max:255|required',
            'last_name_2' => 'string|max:255|required',
            'phone_number' => 'string|min:10|max:10|required',
            'email' => 'email|max:255|required|unique:relatives,email,' . $id,
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

        $person->update([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2
        ]);

        $relative->update([
            'phone_number' => $request->phone_number,
            'email' => $request->email
        ]);

        return response()->json([
            'msg' => 'Relative Updated Successfully',
        ], 200);
    }
    // Listo
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
