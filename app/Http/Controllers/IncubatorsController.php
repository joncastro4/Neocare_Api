<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyIncubator;
use App\Models\Data;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;
use App\Models\UserPerson;
use App\Models\Nurse;
use Illuminate\Support\Facades\Http;

class IncubatorsController extends Controller
{
    public $incubatorNotFound = 'No Incubator Found';

    // Listo
    public function index(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'hospital_id' => $user->role === 'nurse' ? 'sometimes' : 'required|integer|exists:hospitals,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        if ($user->role === 'nurse') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();
            if (!$userPerson) {
                return response()->json(['msg' => 'User person not found for nurse'], 404);
            }

            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();
            if (!$nurse) {
                return response()->json(['msg' => 'Nurse profile not found'], 404);
            }

            $request->merge(['hospital_id' => $nurse->hospital_id]);
        }

        $incubatorsQuery = Incubator::with([
            'room.hospital',
            'baby_incubator' => function ($query) {
                $query->latest()->with(['baby.person', 'nurse.userPerson.person']);
            }
        ]);

        if ($request->hospital_id) {
            $incubatorsQuery->whereHas('room', function ($query) use ($request, $user) {
                $query->where('hospital_id', $request->hospital_id);

                if ($request->room_id && $user->role !== 'nurse') {
                    $query->where('id', $request->room_id);
                }
            });
        }

        $incubators = $incubatorsQuery->orderByDesc('created_at')->paginate(6);

        // Mantener estructura de respuesta incluso cuando está vacío
        $data = $incubators->map(function ($incubator) {
            $lastBabyIncubator = $incubator->baby_incubator->first();

            $babyData = $lastBabyIncubator && $lastBabyIncubator->baby
                ? [
                    'id' => $lastBabyIncubator->baby->id,
                    'name' => $lastBabyIncubator->baby->person->name . ' ' .
                        $lastBabyIncubator->baby->person->last_name_1 . ' ' .
                        ($lastBabyIncubator->baby->person->last_name_2 ?? '')
                ]
                : null;

            $nurseData = $lastBabyIncubator && $lastBabyIncubator->nurse
                ? [
                    'id' => $lastBabyIncubator->nurse->id,
                    'name' => $lastBabyIncubator->nurse->userPerson->person->name . ' ' .
                        $lastBabyIncubator->nurse->userPerson->person->last_name_1 . ' ' .
                        ($lastBabyIncubator->nurse->userPerson->person->last_name_2 ?? '')
                ]
                : null;

            return [
                'id' => $incubator->id,
                'state' => $incubator->state,
                'room_number' => $incubator->room->number,
                'room_id' => $incubator->room->id,
                'nurse_id' => $nurseData['id'] ?? null,
                'nurse' => $nurseData['name'] ?? 'No Nurse',
                'baby' => $babyData['name'] ?? 'No Baby',
                'baby_id' => $babyData['id'] ?? null,
                'created_at' => $incubator->created_at->format('Y-d-m')
            ];
        });

        return response()->json([
            'incubators' => $data,
            'total' => $incubators->total(),
            'per_page' => $incubators->perPage(),
            'current_page' => $incubators->currentPage(),
            'last_page' => $incubators->lastPage(),
        ], 200);
    }

    public function indexNoPaginate(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'hospital_id' => $user->role === 'nurse' ? 'sometimes' : 'required|integer|exists:hospitals,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        if ($user->role === 'nurse') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();
            if (!$userPerson) {
                return response()->json(['msg' => 'User person not found for nurse'], 404);
            }

            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();
            if (!$nurse) {
                return response()->json(['msg' => 'Nurse profile not found'], 404);
            }

            $request->merge(['hospital_id' => $nurse->hospital_id]);
        }

        $incubatorsQuery = Incubator::with([
            'room.hospital',
            'baby_incubator' => function ($query) {
                $query->latest()->with(['baby.person', 'nurse.userPerson.person']);
            }
        ]);

        if ($request->hospital_id) {
            $incubatorsQuery->whereHas('room', function ($query) use ($request, $user) {
                $query->where('hospital_id', $request->hospital_id);

                if ($request->room_id && $user->role !== 'nurse') {
                    $query->where('id', $request->room_id);
                }
            });
        }

        $incubators = $incubatorsQuery->orderBy('created_at')->get();

        // Mantener estructura de respuesta incluso cuando está vacío
        $data = $incubators->map(function ($incubator) {
            $lastBabyIncubator = $incubator->baby_incubator->first();

            $babyData = $lastBabyIncubator && $lastBabyIncubator->baby
                ? [
                    'id' => $lastBabyIncubator->baby->id,
                    'name' => $lastBabyIncubator->baby->person->name . ' ' .
                        $lastBabyIncubator->baby->person->last_name_1 . ' ' .
                        ($lastBabyIncubator->baby->person->last_name_2 ?? '')
                ]
                : null;

            $nurseData = $lastBabyIncubator && $lastBabyIncubator->nurse
                ? [
                    'id' => $lastBabyIncubator->nurse->id,
                    'name' => $lastBabyIncubator->nurse->userPerson->person->name . ' ' .
                        $lastBabyIncubator->nurse->userPerson->person->last_name_1 . ' ' .
                        ($lastBabyIncubator->nurse->userPerson->person->last_name_2 ?? '')
                ]
                : null;

            return [
                'id' => $incubator->id,
                'state' => $incubator->state,
                'room_number' => $incubator->room->number,
                'room_id' => $incubator->room->id,
                'nurse_id' => $nurseData['id'] ?? null,
                'nurse' => $nurseData['name'] ?? 'No Nurse',
                'baby' => $babyData['name'] ?? 'No Baby',
                'baby_id' => $babyData['id'] ?? null,
                'created_at' => $incubator->created_at->format('Y-d-m')
            ];
        });

        return response()->json([
            'incubators' => $data
        ], 200);
    }

    // Listo
    public function store(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'room_id' => 'required|integer|exists:rooms,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $incubator = Incubator::create([
            'room_id' => $request->room_id
        ]);

        Data::create([
            'incubator_id' => $incubator->id
        ]);

        return response()->json([
            'msg' => 'Incubator Created Successfully',
            'incubator' => $incubator
        ], 201);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $user = auth()->user();

        // Buscar la incubadora con su habitación y hospital
        $incubator = Incubator::with('room.hospital')->find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => 'Incubator not found'
            ], 404);
        }

        $latestBabyIncubator = BabyIncubator::where('incubator_id', $id)
            ->orderByDesc('created_at')
            ->first();

        $nurseId = 'No Nurse';
        $nurseFullName = 'No Nurse';
        $babyId = null;
        $babyFullName = 'No Baby';
        $createdAt = null;

        if ($latestBabyIncubator) {
            $latestBabyIncubator->load('baby.person', 'nurse.userPerson.person');

            $nurseId = $latestBabyIncubator->nurse_id ?? 'No Nurse';
            $createdAt = $latestBabyIncubator->created_at ?? null;

            if ($latestBabyIncubator->nurse) {
                $nurse = $latestBabyIncubator->nurse;
                $nurseFullName = $nurse->userPerson->person->name . ' ' .
                    $nurse->userPerson->person->last_name_1 . ' ' .
                    ($nurse->userPerson->person->last_name_2 ?? '');
            }

            if ($latestBabyIncubator->baby) {
                $baby = $latestBabyIncubator->baby;
                $babyId = $baby->id;
                $babyFullName = $baby->person->name . ' ' .
                    $baby->person->last_name_1 . ' ' .
                    ($baby->person->last_name_2 ?? '');
            }
        }

        $data = [
            'id' => $incubator->id,
            'state' => $incubator->state,
            'room_number' => $incubator->room->number,
            'room_id' => $incubator->room->id,
            'nurse_id' => $nurseId,
            'nurse' => $nurseFullName,
            'baby' => $babyFullName,
            'baby_id' => $babyId,
            'created_at' => $createdAt,
            'baby_incubator_id' => $latestBabyIncubator ? $latestBabyIncubator->id : null
        ];

        return response()->json([
            'incubator' => $data
        ], 200);
    }

    // Listo
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $validate = Validator::make($request->all(), [
            'state' => 'required|string|in:active,available',
            'room_id' => 'required|integer|exists:rooms,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $BabyIncubator = BabyIncubator::where('incubator_id', $id)->orderBy('created_at', 'desc')->first();

        if (!$BabyIncubator) {
            return response()->json([
                'msg' => 'BabyIncubator not found'
            ], 404);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->update([
            'state' => $request->state,
            'room_id' => $request->room_id
        ]);

        $BabyIncubator->update([
            'egress_date' => now(),
        ]);

        return response()->json([
            'msg' => 'Incubator Updated Successfully'
        ], 200);
    }
    // Listo
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->delete();

        return response()->json([
            'msg' => 'Incubator Deleted'
        ], 200);
    }
}
