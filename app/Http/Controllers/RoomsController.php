<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
class RoomsController extends Controller
{
    public function index()
    {
        $rooms = Room::with('hospital')->get();
        if (!$rooms) {
            return response()->json([
                'message' => 'No rooms found'
            ], 404);
        }
        return response()->json($rooms, 200);
    }
    public function show($id)
    {
        $room = Room::with('hospital')->find($id);
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }
        return response()->json($room, 200);
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
