<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\BabyIncubator;
use DB;

class BabiesController extends Controller
{
    public function index(Request $request)
    {
<<<<<<< HEAD
        $user = $request->user();

        if ($user->role === 'nurse') 
        {
            $babies = Baby::with('person')
                ->whereHas('nurse_baby', function ($query) use ($user) {
                    $query->whereHas('nurse', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->get();
        } 
        else if ($user->role === 'admin') 
        {
            $babies = Baby::with('person')->get();
        } 
        else 
        {
            return response()->json(['msg' => 'Unauthorized role'], 403);
        }

        if ($babies->isEmpty()) 
        {
            return response()->json(['msg' => "No Babies Found"], 204);
        }

=======
        // Obtener el usuario autenticado
        $user = $request->user();

        // Verificar el rol del usuario
        if ($user->role === 'nurse') {
            // Si el rol es 'nurse', traer solo los bebés relacionados con esa enfermera.
            $babies = Baby::with('person') // Obtener los bebés con su información de persona
                ->whereHas('nurse_baby', function ($query) use ($user) {
                    // Filtrar por la relación a través de la tabla intermedia 'nurses_babies'
                    $query->whereHas('nurse', function ($q) use ($user) {
                        $q->where('user_id', $user->id); // Filtrar por el 'user_id' de la enfermera
                    });
                })
                ->get();
        } elseif ($user->role === 'admin') {
            // Si el rol es 'admin', traer todos los bebés.
            $babies = Baby::with('person')->get();
        } else {
            // Si el rol no es ni 'nurse' ni 'admin', retornar un error.
            return response()->json(['msg' => 'Unauthorized role'], 403);
        }

        // Verificar si no se encontraron bebés
        if ($babies->isEmpty()) {
            return response()->json(['msg' => "No Babies Found"], 204);
        }

        // Retornar los bebés encontrados
>>>>>>> 4347498cf58ee0221e6854dfaf52b970850453de
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
            'ingress_date' => 'nullable|date|after_or_equal:date_of_birth|before_or_equal:today',
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

        $person = new Person();
        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;
        $person->save();

        $baby = new Baby();
        $baby->person_id = $person->id;
        $baby->date_of_birth = $request->date_of_birth;
        $baby->ingress_date = $request->ingress_date ?? now()->toDateString();
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

        return response()->json([
            'msg' => 'Baby assigned to incubator successfully'
        ], 200);
    }
}
