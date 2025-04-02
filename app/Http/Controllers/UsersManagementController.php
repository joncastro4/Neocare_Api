<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Nurse;
use App\Models\UserPerson;
class UsersManagementController extends Controller
{
    public function index()
    {
        $users = User::paginate(9);
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
            'role' => 'required|string|in:super-admin,nurse-admin,nurse,admin',
            'hospital_id' => 'required_if:role,nurse,nurse-admin|integer|exists:hospitals,id'
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
        if ($user->role == $request->role) {
            return response()->json([
                'message' => 'User role is already ' . $request->role
            ], 400);
        }
        if ($user->role == 'super-admin' && ($user->email == 'neocare@gmail.com' || $user->name == 'superAdmin')) {
            return response()->json([
                'message' => 'You cannot remove the last super admin'
            ], 400);
        }
        $user->role = $request->role;
        $user->save();

        if ($request->hospital_id) {

            $userPerson = UserPerson::where('user_id', $user->id)->first();

            if (!$userPerson) {
                return response()->json([
                    'message' => 'User person not found'
                ], 404);
            }

            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

            if (!$nurse) {
                Nurse::create([
                    'user_person_id' => $userPerson->id,
                    'hospital_id' => $request->hospital_id
                ]);
            }
        }
        return response()->json([
            'message' => 'User role updated',
            'user' => $user
        ], 200);
    }
}
