<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();

        if (!$notifications) {
            return response()->json([
                'msg' => 'No Notifications Found'
            ], 204);
        }

        return response()->json([
            'data' => $notifications
        ]);
    }
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nurse_id' => 'required|integer|exists:nurses,id',
            'message' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $notification = Notification::create($request->all());

        if (!$notification) {
            return response()->json([
                'msg' => 'Notification not registered'
            ], 400);
        }

        return response()->json([
            'data' => $notification
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'msg' => 'No Notification Found'
            ], 404);
        }

        return response()->json([
            'data' => $notification
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'message' => 'nullable|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'msg' => 'No Notification Found'
            ], 404);
        }

        $notification->message = $request->message;
        $notification->save();

        return response()->json([
            'data' => $notification
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'msg' => 'No Notification Found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'msg' => 'Notification Deleted'
        ], 200);
    }
}
