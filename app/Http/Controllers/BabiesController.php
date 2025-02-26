<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\BabyIncubator;
use App\Models\Incubator;

class BabiesController extends Controller
{
    // Listo
    public function index(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id',
            'incubator_id' => 'nullable|integer|exists:incubators,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $babies = Baby::with([
            'person',
            'hospital',
            'baby_incubator' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($baby) {
                $baby->incubator_id = $baby->baby_incubator->first()->incubator_id ?? null;
                return $baby;
            });


        if ($request->hospital_id) {
            $babies = $babies->where('hospital_id', $request->hospital_id);
        }

        if ($request->incubator_id) {
            $babies = $babies->whereHas('baby_incubator', function ($query) use ($request) {
                $query->where('incubator_id', $request->incubator_id);
            });
        }

        if ($babies->isEmpty() || !$babies) {
            return response()->json([
                'msg' => 'No Babies Found'
            ], 404);
        }

        return response()->json([
            'babies' => $babies
        ], 200);
    }

    // Listo
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'required|integer|exists:hospitals,id',
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $person = Person::create([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2
        ]);

        Baby::create([
            'hospital_id' => $request->hospital_id,
            'person_id' => $person->id,
            'date_of_birth' => $request->date_of_birth
        ]);

        return response()->json([
            'msg' => 'Baby registered Successfully',
        ], 201);
    }
    // Listo
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby = Baby::with('person', 'hospital', 'baby_incubator.incubator', 'relative')->find($id);

        if (!$baby) {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        $incubator = $baby->baby_incubator->first()->incubator ?? null;
        $incubatorInfo = $incubator ? $incubator->id : "Not assigned";

        return response()->json([
            'msg' => "Baby found",
            'baby' => $baby,
            'incubator' => $incubatorInfo
        ], 200);
    }
    // Listo
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
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
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
        $person->update([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2
        ]);

        $baby->update([
            'date_of_birth' => $request->date_of_birth
        ]);

        return response()->json([
            'msg' => "Baby updated successfully",
        ], 200);
    }
    // Listo
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

    public function assignBabyToIncubator(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'baby_id' => ' required|exists:babies,id',
            'incubator_id' => 'required|exists:incubators,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $babyIncubator = new BabyIncubator();

        $babyIncubator->baby_id = $request->baby_id;
        $babyIncubator->incubator_id = $request->incubator_id;

        $babyIncubator->save();

        $incubator = Incubator::find($request->incubator_id);
        if ($incubator) {
            $incubator->state = 'active';
            $incubator->save();
        }

        return response()->json([
            'msg' => 'Baby assigned to incubator successfully'
        ], 200);
    }
}
