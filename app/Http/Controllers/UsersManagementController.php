<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UsersManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        if (!$users) {
            return response()->json([
                'message' => 'No users found'
            ], 404);
        }
        return response()->json($users, 200);
    }
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        return response()->json($user, 200);
    }
    public function roleManagement(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user' => 'required|integer|exists:users,id',
            'role' => 'required|string|in:super-admin,nurse-admin,nurse,user',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 400);
        }
        $user = User::find($request->user);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->role = $request->role;
        $user->save();
        return response()->json($user, 200);
    }
}
