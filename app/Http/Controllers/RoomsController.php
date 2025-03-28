<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use App\Models\BabyIncubator;
use App\Models\Incubator;
use Illuminate\Validation\Rule;
class RoomsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $query = Room::with('hospital');

        if ($request->hospital_id) {
            $query->where('hospital_id', $request->hospital_id);
        }

        if ($user->role == 'nurse-admin' || $user->role == 'nurse') {
            $query->where('hospital_id', $user->nurse->hospital_id);
        }

        $rooms = $query->paginate(10);

        $rooms->getCollection()->transform(function ($room) {
            $room->created_at = $room->created_at->format('Y-m-d');
            return $room;
        });

        return response()->json([
            'rooms' => $rooms
        ], 200);
    }

    public function indexNoPaginate(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'hospital_id' => 'nullable|integer|exists:hospitals,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $query = Room::with('hospital');

        if ($request->hospital_id) {
            $query->where('hospital_id', $request->hospital_id);
        }

        if ($user->role == 'nurse-admin' || $user->role == 'nurse') {
            $query->where('hospital_id', $user->nurse->hospital_id);
        }

        $rooms = $query->get();

        $rooms->transform(function ($room) {
            $room->created_at = $room->created_at->format('Y-m-d');
            return $room;
        });

        return response()->json([
            'rooms' => $rooms
        ], 200);
    }

    public function show($id)
    {
        $room = Room::with('hospital')->find($id);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $babies = BabyIncubator::whereHas('incubator', function ($query) use ($id) {
            $query->where('room_id', $id);
        })->get();

        $incubators = Incubator::with('baby_incubator.baby.person', 'baby_incubator.nurse.userPerson.person')->where('room_id', $id)->get();

        $formattedIncubators = $incubators->map(function ($incubator) {
            $babyIncubator = $incubator->baby_incubator->first();

            $babyFullName = $babyIncubator?->baby?->person
                ? trim(
                    $babyIncubator->baby->person->name . ' ' .
                    $babyIncubator->baby->person->last_name_1 . ' ' .
                    ($babyIncubator->baby->person->last_name_2 ?? '')
                )
                : 'No baby assigned';

            $nurseFullName = $babyIncubator?->nurse?->userPerson?->person
                ? trim(
                    $babyIncubator->nurse->userPerson->person->name . ' ' .
                    $babyIncubator->nurse->userPerson->person->last_name_1 . ' ' .
                    ($babyIncubator->nurse->userPerson->person->last_name_2 ?? '')
                )
                : 'No nurse assigned';

            return [
                'id' => $incubator->id,
                'state' => $incubator->state,
                'created_at' => $incubator->created_at->format('Y-m-d'),
                'baby_full_name' => $babyFullName,
                'nurse_full_name' => $nurseFullName
            ];
        });

        return response()->json([
            'room' => $room,
            'total_babies' => $babies->count(),
            'total_incubators' => $incubators->count(),
            'incubators' => $formattedIncubators
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'required|integer|exists:hospitals,id',
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('hospital_id', $request->hospital_id);
                }),
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $room = Room::create($request->all());
        if (!$room) {
            return response()->json([
                'message' => 'Room not created'
            ], 404);
        }
        return response()->json([
            'message' => 'Room created successfully',
        ], 201);
    }
    public function update(Request $request, $id)
    {

        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('rooms')
                    ->where('hospital_id', $room->hospital_id)
                    ->ignore($room->id),
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $room->update($request->all());

        return response()->json([
            'message' => 'Room updated successfully'
        ], 200);
    }
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }
        $room->delete();
        return response()->json([
            'message' => 'Room deleted'
        ], 200);
    }
}
