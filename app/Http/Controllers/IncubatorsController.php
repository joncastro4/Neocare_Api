<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyIncubator;
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
            'hospital_id' => 'required|integer|exists:hospitals,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $incubatorsQuery = Incubator::with([
            'room.hospital',
            'baby_incubator.baby.person',
            'baby_incubator.nurse.userPerson.person'
        ])->whereHas('room', function ($query) use ($request) {
            $query->where('hospital_id', $request->hospital_id);
            if ($request->room_id) {
                $query->where('id', $request->room_id);
            }
        });

        if ($user->role === 'nurse') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();
            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

            if (!$nurse) {
                return response()->json(['msg' => 'No Nurse Found'], 404);
            }

            $incubatorsQuery->whereHas('baby_incubator', function ($query) use ($nurse) {
                $query->where('nurse_id', $nurse->id);
            });
        }

        $incubators = $incubatorsQuery->orderByDesc('created_at')->paginate(6);

        if ($incubators->isEmpty()) {
            return response()->json(['msg' => 'No Incubators Found'], 404);
        }

        $data = $incubators->map(function ($incubator) use ($user) {
            $babyFullName = 'No Baby';
            $babyId = null;
            $nurseFullName = 'No Nurse';
            $nurseId = null;

            $lastBabyIncubator = $incubator->baby_incubator->sortByDesc('created_at')->first();

            if ($lastBabyIncubator) {
                if ($lastBabyIncubator->baby) {
                    $baby = $lastBabyIncubator->baby;
                    $babyFullName = $baby->person->name . ' ' .
                        $baby->person->last_name_1 . ' ' .
                        ($baby->person->last_name_2 ?? '');
                    $babyId = $baby->id;
                }

                if ($lastBabyIncubator->nurse) {
                    $nurse = $lastBabyIncubator->nurse;
                    $nurseFullName = $nurse->userPerson->person->name . ' ' .
                        $nurse->userPerson->person->last_name_1 . ' ' .
                        ($nurse->userPerson->person->last_name_2 ?? '');
                    $nurseId = $nurse->id;
                }
            }

            return [
                'id' => $incubator->id,
                'state' => $incubator->state,
                'room_number' => $incubator->room->number,
                'room_id' => $incubator->room->id,
                'nurse_id' => $nurseId,
                'nurse' => $nurseFullName,
                'baby' => $babyFullName,
                'baby_id' => $babyId,
                'created_at' => $incubator->created_at->format('Y-d-m') // Formato YYYY-DD-MM
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

        Incubator::create([
            'room_id' => $request->room_id
        ]);

        return response()->json([
            'msg' => 'Incubator Created Successfully',
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
