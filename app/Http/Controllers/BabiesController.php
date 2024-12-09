<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\BabyIncubator;
use DB;
use DateTime;
use Carbon\Carbon;
use App\Models\Incubator;

class BabiesController extends Controller
{
    public function index(Request $request)
    {
        $babies = Baby::with('person')->orderBy('created_at', 'desc')->get();

        if ($babies->isEmpty()) {
            return response()->json(['msg' => "No Babies Found"], 204);
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
            'date_of_birth' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Valida el formato dd/MM/yyyy
                    $date = DateTime::createFromFormat('d/m/Y', $value);
                    if (!$date || $date->format('d/m/Y') !== $value) {
                        $fail('The ' . $attribute . ' must be in the format dd/MM/yyyy.');
                    }
                },
                'before_or_equal:today',
            ],
            'ingress_date' => [
                'nullable', // Permitir que no se envíe
                'string',
                function ($attribute, $value, $fail) {
                    // Si se envía, valida el formato
                    if ($value) {
                        $date = DateTime::createFromFormat('d/m/Y', $value);
                        if (!$date || $date->format('d/m/Y') !== $value) {
                            $fail('The ' . $attribute . ' must be in the format dd/MM/yyyy.');
                        }
                    }
                },
                'after_or_equal:date_of_birth',
                'before_or_equal:today',
            ],
            'egress_date' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // Valida el formato dd/MM/yyyy
                    $date = DateTime::createFromFormat('d/m/Y', $value);
                    if (!$date || $date->format('d/m/Y') !== $value) {
                        $fail('The ' . $attribute . ' must be in the format dd/MM/yyyy.');
                        return;
                    }

                    $ingressDate = $request->input('ingress_date', now()->format('d/m/Y'));
                    $ingressDateParsed = DateTime::createFromFormat('d/m/Y', $ingressDate);

                    if ($ingressDateParsed && $date < $ingressDateParsed) {
                        $fail('The ' . $attribute . ' cannot be earlier than the ingress date.');
                    }
                },
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $dateOfBirth = Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d');
        $egressDate = Carbon::createFromFormat('d/m/Y', $request->egress_date)->format('Y-m-d');

        $person = new Person();
        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;
        $person->save();

        $baby = new Baby();
        $baby->person_id = $person->id;
        $baby->date_of_birth = $dateOfBirth;
        $baby->ingress_date = $request->ingress_date ?? now()->toDateString();
        $baby->egress_date = $egressDate;
        $baby->save();

        return response()->json([
            'message' => 'Baby registered Successfully',
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $baby = Baby::with('person')->find($id);

        if (!$baby) {
            return response()->json([
                'msg' => "Baby not found"
            ], 404);
        }

        return response()->json([
            'msg' => "Baby found",
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
