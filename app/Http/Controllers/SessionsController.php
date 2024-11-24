<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Person;
use App\Models\Nurse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\RegisterMail;
use App\Mail\NurseActivatedNotification;


class SessionsController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:32|confirmed',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Invalid email'
            ], 400);
        }

        // Registrar usuario
        $user = new User();

        $user->name = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // Registrar persona
        $person = new Person();

        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;

        $person->save();

        // Registrar enfermera
        $nurse = new Nurse();

        $nurse->user_id = $user->id;
        $nurse->person_id = $person->id;

        $nurse->save();

        $signedUrl = URL::temporarySignedRoute(
            'verify-email',
            now()->addMinutes(1),
            ['user' => $user->id]
        );

        Mail::to($request->email)->send(new RegisterMail($request->name, $request->email, $signedUrl));

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where('id', $request->user)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->email_verified_at = now();
        $user->save();

        $nurse = Nurse::where('user_id', $user->id)->first();

        if (!$nurse) {
            return response()->json([
                'message' => 'Nurse not found'
            ], 404);
        }

        $person = Person::where('id', $nurse->person_id)->first();

        if (!$person) {
            return response()->json([
                'message' => 'Person not found'
            ]);
        }

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found'
            ]);
        }

        $signedUrl = URL::signedRoute('nurse-activate', ['id' => $user->id]);

        Mail::to($admin->email)->send(new NurseActivatedNotification($user, $person, $signedUrl));

        return response()->json([
            'message' => 'Email verified successfully'
        ], 200);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->where('email_verified_at', '!=', null)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials, or email not verified'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

    public function resend_activation(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        $signedUrl = URL::temporarySignedRoute(
            'verify-email',
            now()->addMinutes(15),
            ['user' => $user->id]
        );

        Mail::to($request->email)->send(new RegisterMail($user->name, $user->email, $signedUrl));

        return response()->json([
            'message' => 'Email sent'
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $nurse = Nurse::with('person')->where('user_id', $user->id)->first();

        if (!$nurse) {
            return response()->json([
                'message' => 'Nurse not found'
            ], 404);
        }

        return response()->json([
            'message' => 'User data',
            'user' => $user,
            'nurse' => $nurse
        ], 200);
    }
    public function activateNurse($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email not verified'
            ], 400);
        }

        if ($user->role == 'nurse') {
            return response()->json([
                'message' => 'User already activated'
            ], 400);
        }

        if ($user->role == 'admin') {
            return response()->json([
                'message' => 'Admin cannot be activated'
            ], 400);
        }

        $user->role = 'nurse';
        $user->save();

        return response()->json([
            'message' => 'Nurse activated successfully'
        ], 200);
    }

}
