<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Check;
use App\Models\Nurse;
use Illuminate\Support\Facades\Validator;
use App\Models\UserPerson;
use Laravel\Sanctum\PersonalAccessToken;

class ChecksController extends Controller
{
    public function index(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id',
            'nurse_id' => 'nullable|integer|exists:nurses,id',
            'baby_id' => 'nullable|integer|exists:babies,id',
            'incubator_id' => 'nullable|integer|exists:incubators,id',
            'date1' => 'nullable|date|before_or_equal:date2|before_or_equal:today',
            'date2' => 'nullable|date|after_or_equal:date1|before_or_equal:today',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $token = $request->bearerToken();

        $user = PersonalAccessToken::findToken($token)->tokenable;

        if ($user->role == 'nurse' || $user->role == 'nurse-admin') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();

            if (!$userPerson) {
                return response()->json([
                    'message' => 'UserPerson not found'
                ], 404);
            }

            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

            if (!$nurse) {
                return response()->json([
                    'message' => 'Nurse not found'
                ], 404);
            }

            $hospital = $nurse->hospital;

            $checksQuery = Check::query()->with(['nurse.userPerson.person', 'baby_incubator.baby.person']);

            if ($user->role == 'nurse-admin') {
                $nurses = Nurse::where('hospital_id', $hospital->id)->pluck('id');
                $checksQuery->whereIn('nurse_id', $nurses);
            } else {
                $checksQuery->where('nurse_id', $nurse->id);
            }

            $checks = $checksQuery->orderByDesc('id')->paginate(9);

        } else if ($user->role == 'super-admin' || $user->role == 'admin') {
            $checksQuery = Check::query()->with(['nurse.userPerson.person', 'baby_incubator.baby.person']);

            if ($request->hospital_id) {
                $nurses = Nurse::where('hospital_id', $request->hospital_id)->pluck('id');
                $checksQuery->whereIn('nurse_id', $nurses);
            }

            if ($request->nurse_id) {
                $checksQuery->where('nurse_id', $request->nurse_id);
            }

            if ($request->baby_id) {
                $checksQuery->whereHas('baby_incubator.baby', function ($query) use ($request) {
                    $query->where('id', $request->baby_id);
                });
            }

            if ($request->incubator_id) {
                $checksQuery->where('baby_incubator_id', $request->incubator_id);
            }

            if ($request->date1 && $request->date2) {
                $checksQuery->whereBetween('created_at', [$request->date1, $request->date2]);
            }

            $checks = $checksQuery->orderByDesc('id')->paginate(6);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($checks->isEmpty()) {
            return response()->json([
                'message' => 'No checks found'
            ], 404);
        }

        $checks->getCollection()->transform(function ($check) use ($user) {
            $nurse = $check->nurse;
            $nursePerson = $nurse ? $nurse->userPerson->person : null;

            $nurseFullName = null;
            if ($user->role != 'nurse') {
                $nurseFullName = $nursePerson ? $nursePerson->name . ' ' . $nursePerson->last_name_1 . ' ' . $nursePerson->last_name_2 : null;
            }

            $babyIncubator = $check->baby_incubator;
            $baby = $babyIncubator ? $babyIncubator->baby : null;
            $babyPerson = $baby ? $baby->person : null;
            $babyFullName = $babyPerson ? $babyPerson->name . ' ' . $babyPerson->last_name_1 . ' ' . $babyPerson->last_name_2 : null;
            $incubator = $babyIncubator ? $babyIncubator->incubator->id : null;

            return [
                'check_id' => $check->id,
                'title' => $check->title,
                'description' => $check->description,
                'nurse' => $nurseFullName,
                'baby' => $babyFullName,
                'incubator' => $incubator,
                'created_at' => $check->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data' => $checks
        ], 200);
    }

    public function indexNoPaginate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id',
            'nurse_id' => 'nullable|integer|exists:nurses,id',
            'baby_id' => 'nullable|integer|exists:babies,id',
            'incubator_id' => 'nullable|integer|exists:incubators,id',
            'date1' => 'nullable|date|before_or_equal:date2|before_or_equal:today',
            'date2' => 'nullable|date|after_or_equal:date1|before_or_equal:today',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $token = $request->bearerToken();
        $user = PersonalAccessToken::findToken($token)->tokenable;

        if ($user->role == 'nurse' || $user->role == 'nurse-admin') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();

            if (!$userPerson) {
                return response()->json([
                    'message' => 'UserPerson not found'
                ], 404);
            }

            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

            if (!$nurse) {
                return response()->json([
                    'message' => 'Nurse not found'
                ], 404);
            }

            $hospital = $nurse->hospital;
            $checksQuery = Check::query()->with(['nurse.userPerson.person', 'baby_incubator.baby.person']);

            if ($user->role == 'nurse-admin') {
                $nurses = Nurse::where('hospital_id', $hospital->id)->pluck('id');
                $checksQuery->whereIn('nurse_id', $nurses);
            } else {
                $checksQuery->where('nurse_id', $nurse->id);
            }

            $checks = $checksQuery->orderByDesc('id')->get();

        } else if ($user->role == 'super-admin' || $user->role == 'admin') {
            $checksQuery = Check::query()->with(['nurse.userPerson.person', 'baby_incubator.baby.person']);

            if ($request->hospital_id) {
                $nurses = Nurse::where('hospital_id', $request->hospital_id)->pluck('id');
                $checksQuery->whereIn('nurse_id', $nurses);
            }

            if ($request->nurse_id) {
                $checksQuery->where('nurse_id', $request->nurse_id);
            }

            if ($request->baby_id) {
                $checksQuery->whereHas('baby_incubator.baby', function ($query) use ($request) {
                    $query->where('id', $request->baby_id);
                });
            }

            if ($request->incubator_id) {
                $checksQuery->where('baby_incubator_id', $request->incubator_id);
            }

            if ($request->date1 && $request->date2) {
                $checksQuery->whereBetween('created_at', [$request->date1, $request->date2]);
            }

            $checks = $checksQuery->orderByDesc('id')->get();
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($checks->isEmpty()) {
            return response()->json([
                'data' => $checks
            ], 200);
        }

        $transformedChecks = $checks->map(function ($check) use ($user) {
            $nurse = $check->nurse;
            $nursePerson = $nurse ? $nurse->userPerson->person : null;

            $nurseFullName = null;
            if ($user->role != 'nurse') {
                $nurseFullName = $nursePerson ? $nursePerson->name . ' ' . $nursePerson->last_name_1 . ' ' . $nursePerson->last_name_2 : null;
            }

            $babyIncubator = $check->baby_incubator;
            $baby = $babyIncubator ? $babyIncubator->baby : null;
            $babyPerson = $baby ? $baby->person : null;
            $babyFullName = $babyPerson ? $babyPerson->name . ' ' . $babyPerson->last_name_1 . ' ' . $babyPerson->last_name_2 : null;
            $incubator = $babyIncubator ? $babyIncubator->incubator->id : null;

            return [
                'check_id' => $check->id,
                'title' => $check->title,
                'description' => $check->description,
                'nurse' => $nurseFullName,
                'baby' => $babyFullName,
                'incubator' => $incubator,
                'created_at' => $check->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'data' => $transformedChecks
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }

        $user_id = $user->id;

        $userPerson = UserPerson::where('user_id', $user_id)->first();

        if (!$userPerson) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }

        $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

        if (!$nurse) {
            return response()->json([
                'msg' => 'Unauthorized'
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'baby_incubator_id' => 'required|integer|exists:babies_incubators,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $check = Check::create([
            'nurse_id' => $nurse->id,
            'baby_incubator_id' => $request->baby_incubator_id,
            'title' => $request->title,
            'description' => $request->description
        ]);

        if (!$check) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'msg' => 'Chequeo registrado exitosamente',
            'data' => $check
        ], 200);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $check = Check::with(['nurse.userPerson.person', 'baby_incubator.baby.person'])->find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Check Found'
            ], 404);
        }

        $data = [
            'check_id' => $check->id,
            'title' => $check->title,
            'description' => $check->description,
            'nurse' => $check->nurse->userPerson->person->name . ' ' . $check->nurse->userPerson->person->last_name_1 . ' ' . $check->nurse->userPerson->person->last_name_2,
            'baby' => $check->baby_incubator->baby->person->name . ' ' . $check->baby_incubator->baby->person->last_name_1 . ' ' . $check->baby_incubator->baby->person->last_name_2,
            'incubator' => $check->baby_incubator->incubator->id,
            'created_at' => $check->created_at->format('Y-m-d H:i:s')
        ];


        return response()->json([
            'data' => $data
        ]);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Check Found'
            ], 404);
        }

        $check->update([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'msg' => 'Check Updated Successfully',
            'data' => $check
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $check = Check::find($id);

        if (!$check) {
            return response()->json([
                'msg' => 'No Check Found'
            ], 404);
        }

        $check->delete();

        return response()->json([
            'msg' => 'Check Deleted'
        ], 200);
    }
}