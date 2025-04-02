<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Baby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\BabyIncubator;
use App\Models\Incubator;
use App\Models\UserPerson;
use App\Models\Nurse;
use Carbon\Carbon;
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

        
        $query = Baby::with([
            'person',
            'hospital',
            'baby_incubator' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->orderBy('created_at', 'desc');

        if ($request->hospital_id) {
            $query->where('hospital_id', $request->hospital_id);
        }

        if ($request->incubator_id) {
            $query->whereHas('baby_incubator', function ($q) use ($request) {
                $q->where('incubator_id', $request->incubator_id);
            });
        }

       
        $babies = $query->paginate(10)->through(function ($baby) {
            $baby->incubator_id = $baby->baby_incubator->first()->incubator_id ?? null;
            $baby->created_at_formatted = Carbon::parse($baby->created_at)->format('Y-m-d');
            return $baby;
        });

        if ($babies->isEmpty()) {
            return response()->json([
                'msg' => 'No Babies Found'
            ], 404);
        }

        return response()->json($babies, 200);
    }

    public function indexNoPaginate(Request $request){
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id'
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
    
        $query = Baby::with([
            'person',
            'hospital',
            'baby_incubator' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->orderBy('created_at', 'desc');
    
        if ($request->hospital_id) {
            $query->where('hospital_id', $request->hospital_id);
        }
    
        if ($request->incubator_id) {
            $query->whereHas('baby_incubator', function ($q) use ($request) {
                $q->where('incubator_id', $request->incubator_id);
            });
        }
    
        $babies = $query->get();
    
        if ($babies->isEmpty()) {
            return response()->json([
                'msg' => 'No Babies Found'
            ], 404);
        }
    
        $transformedBabies = $babies->map(function ($baby) {
            $currentIncubatorId = $baby->baby_incubator->isNotEmpty() 
                ? $baby->baby_incubator->first()->incubator_id 
                : null;
    
            return [
                'id' => $baby->id,
                'date_of_birth' => $baby->date_of_birth,
                'created_at' => $baby->created_at->format('Y-m-d'),
                'full_name' => $baby->person ? 
                    trim($baby->person->name . ' ' . $baby->person->last_name_1 . ' ' . $baby->person->last_name_2) : 
                    null,
                'incubator_id' => $currentIncubatorId,
                
            ];
        });
    
        return response()->json([
            'data' => $transformedBabies
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
            return response()->json($validate->errors(), 422);
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
        $baby = Baby::with('person', 'hospital', 'baby_incubator.incubator', 'relative.person')->find($id);

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
            return response()->json($validate->errors(), 422);
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

    // Listo
    public function assignBabyToIncubator(Request $request)
    {

        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'baby_id' => ' required|exists:babies,id',
            'incubator_id' => 'required|exists:incubators,id',
            'nurse_id' => 'required|exists:nurses,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        if ($user->role != 'nurse-admin' && $user->role != 'nurse') {
            if (!$request->nurse_id) {
                return response()->json([
                    'errors' => 'Nurse id is required'
                ], 422);
            }
            $nurse_id = $request->nurse_id;
        } else {
            $userPerson = UserPerson::where('user_id', $user->id)->first();
            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();
            $nurse_id = $nurse->id;
        }



        $babyIncubator = BabyIncubator::create([
            'baby_id' => $request->baby_id,
            'incubator_id' => $request->incubator_id,
            'hospital_id' => $request->hospital_id,
            'nurse_id' => $nurse_id
        ]);

        $incubator = Incubator::find($babyIncubator->incubator_id);

        if ($incubator) {
            $incubator->state = 'active';
            $incubator->save();
        }

        return response()->json([
            'msg' => 'Baby assigned to incubator successfully'
        ], 200);
    }

}