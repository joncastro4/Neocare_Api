<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nurse;
use App\Models\UserPerson;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class ProfileController extends Controller
{
    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $userperson = UserPerson::where('user_id', $user->id)->first();

        if (!$userperson) {
            return response()->json([
                'message' => 'UserPerson not found'
            ], 404);
        }

        $person = Person::where('id', $userperson->person_id)->first();

        if (!$person) {
            return response()->json([
                'message' => 'Person not found'
            ], 404);
        }

        $nurse = Nurse::where('user_person_id', $userperson->id)->first();

        return response()->json([
            'message' => 'User data',
            'user' => $user,
            'person' => $person,
            'nurse' => $nurse ?? null,
        ], 200);
    }
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,name,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|max:13',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->username,
            'email' => $request->email,
        ]);

        $userperson = UserPerson::where('user_id', $user->id)->first();

        if (!$userperson) {
            return response()->json([
                'message' => 'UserPerson not found'
            ], 404);
        }

        $person = Person::where('id', $userperson->person_id)->first();

        if (!$person) {
            return response()->json([
                'message' => 'Person not found'
            ], 404);
        }

        $person->update([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2
        ]);

        $nurse = Nurse::where('user_person_id', $userperson->id)->first();

        if ($nurse) {
            $nurse->update([
                'rfc' => $request->rfc
            ]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
            'person' => $person,
            'nurse' => $nurse ?? null,
        ], 200);
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->role == 'super-admin') {
            return response()->json([
                'message' => 'Superadmin cannot be deleted'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid password'
            ], 401);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
