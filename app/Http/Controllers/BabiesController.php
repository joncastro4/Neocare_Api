<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;

class BabiesController extends Controller
{
    public function index()
    {
        $babies = Baby::all();

        if (!$babies) {
            return response()->json([
                'msg' => "There are no babies registered"
            ], 204);
        }

        return response()->json([
            'babies' => $babies
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'ingress_date' => 'sometimes|date|after_or_equal:date_of_birth|before_or_equal:today',
            'egress_date' => [
                'nullable',
                'date',
                'after_or_equal:date_of_birth',
                function ($value, $fail) use ($request) {
                    $ingressDate = $request->ingress_date ?? now()->toDateString();
                    if ($value < $ingressDate) {
                        $fail('The egress date cannot be earlier than the ingress date.');
                    }
                },
            ],
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

        $baby = new Baby();
        $baby->person_id = $person->id;
        $baby->date_of_birth = $request->date_of_birth;
        $baby->ingress_date = $request->ingress_date ?? now()->toDateString();
        $baby->egress_date = $request->egress_date;
        $baby->save();

        if (!$baby) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'msg' => 'Baby registered Successfully',
            'person' => $person,
            'baby' => $baby
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby = Baby::find($id);

        if (!$baby) {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $person = Person::find($baby->person_id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }

        return response()->json([
            'msg' => "Baby found",
            'person' => $person,
            'baby' => $baby
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'ingress_date' => 'sometimes|date|after_or_equal:date_of_birth|before_or_equal:today',
            'egress_date' => [
                'nullable',
                'date',
                'after_or_equal:date_of_birth',
                function ($value, $fail) use ($request) {
                    $ingressDate = $request->ingress_date ?? now()->toDateString();
                    if ($value < $ingressDate) {
                        $fail('The egress date cannot be earlier than the ingress date.');
                    }
                },
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $baby = Baby::find($id);

        if (!$baby) {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $person = Person::find($baby->person_id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }
        $person->name = $request->name ?? $person->name;
        $person->last_name_1 = $request->last_name_1 ?? $person->last_name_1;
        $person->last_name_2 = $request->last_name_2 ?? $person->last_name_2;
        $person->save();

        $baby->date_of_birth = $request->date_of_birth ?? $baby->date_of_birth;
        $baby->ingress_date = $request->ingress_date ?? $baby->ingress_date;
        $baby->egress_date = $request->egress_date ?? $baby->egress_date;
        $baby->save();

        return response()->json([
            'msg' => "Baby updated successfully",
            'person' => $person,
            'baby' => $baby
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby = Baby::find($id);

        if (!$baby) {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $person = Person::find($baby->person_id);

        if (!$person) {
            return response()->json([
                'msg' => "Person not found"
            ], 404);
        }

        $person->delete();
        $baby->delete();

        return response()->json([
            'msg' => "Baby deleted"
        ], 200);
    }
}
