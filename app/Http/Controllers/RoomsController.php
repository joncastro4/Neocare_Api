<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use App\Models\BabyIncubator;
use App\Models\Incubator;
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

        $rooms = Room::with('hospital')->paginate(9);

        if ($request->hospital_id) {
            $rooms = Room::with('hospital')->where('hospital_id', $request->hospital_id)->get();
        }

        if ($user->role == 'nurse-admin' || $user->role == 'nurse') {
            $rooms = Room::with('hospital')->where('hospital_id', $user->nurse->hospital_id)->get();
        }

        if (!$rooms || $rooms->isEmpty()) {
            return response()->json([
                'message' => 'No rooms found'
            ], 404);
        }
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
        $incubators = Incubator::where('room_id', $id)->get();

        return response()->json([
            'room' => $room,
            'total_babies' => $babies->count(),
            'total_incubators' => $incubators->count(),
            'incubators' => $incubators
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'hospital_id' => 'required|integer|exists:hospitals,id',
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|unique:rooms,number',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $room = Room::create($request->all());
        if (!$room) {
            return response()->json([
                'message' => 'Room not created'
            ], 404);
        }
        return response()->json($room, 200);
    }
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1|unique:rooms,number,' . $id,
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 400);
        }

        $room = Room::find($id);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $room->update($request->all());

        return response()->json($room, 200);
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
