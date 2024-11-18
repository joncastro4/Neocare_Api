<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Validator;

class SchedulesController extends Controller
{
    public function index()
    {
        $schedules = Schedule::all();

        if (!$schedules) {
            return response()->json([
                'msg' => 'No Schedules Found'
            ], 204);
        }

        return response()->json([
            'data' => $schedules
        ], 200);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nurse_id' => 'required|integer|exists:nurses,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $schedule = Schedule::create($request->all());

        return response()->json([
            'data' => $schedule
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json([
                'msg' => 'No Schedule Found'
            ], 404);
        }

        return response()->json([
            'data' => $schedule
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'nurse_id' => 'integer|exists:nurses,id|nullable',
            'day' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday|nullable',
            'start_time' => 'date_format:H:i:s|nullable',
            'end_time' => 'date_format:H:i:s|nullable',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json([
                'msg' => 'No Schedule Found'
            ], 404);
        }

        $schedule->nurse_id = $request->nurse_id ?? $schedule->nurse_id;
        $schedule->day = $request->day ?? $schedule->day;
        $schedule->start_time = $request->start_time ?? $schedule->start_time;
        $schedule->end_time = $request->end_time ?? $schedule->end_time;
        $schedule->save();

        return response()->json([
            'data' => $schedule
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json([
                'msg' => 'No Schedule Found'
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'msg' => 'Schedule Deleted'
        ], 200);
    }
}
