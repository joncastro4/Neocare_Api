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
use App\Mail\UserAccessNotification;
use App\Models\UserPerson;
use Illuminate\View\View;

class SessionsController extends Controller
{
    public function registerWeb(Request $request)
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
        $user = $this->createUser($request);

        // Registrar persona
        $person = $this->createPerson($request);

        // Relacionar usuario y persona
        $this->createUserPerson($user, $person);

        // Enviar correo de verificación
        $this->sendVerificationEmail($user, $user->name, $user->email);

        // Devolver respuesta
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
    public function registerApp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:32|confirmed',
            'hospital_id' => 'required|integer|exists:hospitals,id',
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
        $user = $this->createUser($request);
        // Registrar persona
        $person = $this->createPerson($request);

        // Registrar usuario_persona
        $userPerson = $this->createUserPerson($user, $person);

        // Registrar enfermera
        Nurse::create([
            'user_person_id' => $userPerson->id,
            'hospital_id' => $request->hospital_id
        ]);

        $this->sendVerificationEmail($user, $request->name, $request->email, true);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    private function createPerson(Request $request)
    {
        $person = Person::create([
            'name' => $request->name,
            'last_name_1' => $request->last_name_1,
            'last_name_2' => $request->last_name_2,
        ]);
        return $person;
    }
    private function createUserPerson(User $user, Person $person)
    {
        $userPerson = UserPerson::create([
            'user_id' => $user->id,
            'person_id' => $person->id,
        ]);
        return $userPerson;
    }

    private function createUser(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return $user;
    }
    private function sendVerificationEmail(User $user, $name, $email, $isApp = false)
    {
        $routeName = $isApp ? 'verify-email-app' : 'verify-email-web';
        $signedUrl = URL::signedRoute(
            $routeName,
            ['user' => $user->id]
        );

        Mail::to($email)->send(new RegisterMail($name, $email, $signedUrl));
    }

    public function verifyEmailApp(Request $request)
    {
        $user = $this->verifyUserEmail($request);

        // Verifica si $user es una instancia de la vista
        if ($user instanceof View) {
            return $user;
        }

        // Buscar enfermera del usuario
        $nurse = $user->nurse;

        if (!$nurse) {
            return view('errors.nurse-not-found', ['message' => 'Nurse not found']);
        }

        // Buscar persona del usuario
        $person = $nurse->userPerson->person;

        if (!$person) {
            return view('errors.person-not-found', ['message' => 'Person not found']);
        }

        // Buscar al administrador enfermero del hospital
        $admin = User::whereHas('nurse', function ($query) use ($nurse) {
            $query->where('hospital_id', $nurse->hospital_id)
                ->where('role', 'nurse-admin');
        })->first();

        // Si no se encuentra al administrador enfermero, se busca al super administrador
        if (!$admin) {
            $admin = User::where('role', 'super-admin')->first();
            if (!$admin) {
                return view('errors.admin-not-found', ['message' => 'Admin not found']);
            }
        }

        // Crear URL firmada para activar la cuenta de enfermero
        $signedUrl = URL::signedRoute('nurse-activate', ['id' => $user->id]);

        // Enviar correo de verificación al administrador enfermero o super administrador
        Mail::to($admin->email)->send(new NurseActivatedNotification($user, $person, $signedUrl));

        return view('success.email-verified', ['user' => $user, 'person' => $person, 'message' => 'Email verified successfully']);
    }

    public function verifyEmailWeb(Request $request)
    {
        $user = $this->verifyUserEmail($request);

        // Verifica si $user es una instancia de la vista para devolverla
        if ($user instanceof View) {
            return $user;
        }

        $person = $user->people()->first();

        $admin = User::where('role', 'super-admin')->first();

        if (!$admin) {
            return view('errors.admin-not-found', ['message' => 'Admin not found']);
        }

        $signedUrl = URL::signedRoute('user-activate', ['id' => $user->id]);

        Mail::to($admin->email)->send(new UserAccessNotification($user, $person, $signedUrl));

        return view('success.email-verified', ['user' => $user, 'person' => $person, 'message' => 'Email verified successfully']);
    }

    public function verifyUserEmail(Request $request)
    {
        $user = User::where('id', $request->user)->first();

        if (!$user) {
            return view('errors.user-not-found', ['message' => 'User not found']);
        }

        if ($user->email_verified_at) {
            return view('errors.email-already-verified', ['message' => 'Email already verified']);
        }

        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    public function login(Request $request, $isWeb = false)
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

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials, or email not verified'
            ], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email not verified'
            ], 401);
        }

        if ($user->role == 'guest') {
            return response()->json([
                'message' => 'Not verified by an admin'
            ], 401);
        }

        if ($isWeb && !in_array($user->role, ['super-admin', 'admin'])) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'role' => $user->role
        ], 200);
    }
    public function loginApp(Request $request)
    {
        return $this->login($request);
    }
    public function loginWeb(Request $request)
    {
        return $this->login($request, True);
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

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        $this->sendVerificationEmail($user, $user->name, $user->email);

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
    public function userRole()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'message' => 'User role',
            'role' => $user->role
        ]);
    }
    public function activateNurse($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return view('errors.user-not-found', [
                'message' => 'User not found',
            ]);
        }

        if (!$user->email_verified_at) {
            return view('errors.email-not-verified', [
                'message' => 'Email not verified',
            ]);
        }

        if ($user->role == 'nurse') {
            return view('errors.already-activated', [
                'message' => 'User already activated as a nurse',
            ]);
        }

        if ($user->role == 'admin') {
            return view('errors.admin-cannot-be-activated', [
                'message' => 'Admin cannot be activated as a nurse',
            ]);
        }

        $user->role = 'nurse';
        $user->save();

        return view('success.nurse-verified', [
            'message' => 'Nurse activated successfully',
            'user' => $user,
        ]);
    }
    public function activateUser($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return view('errors.user-not-found', [
                'message' => 'User not found',
            ]);
        }

        if (!$user->email_verified_at) {
            return view('errors.email-not-verified', [
                'message' => 'Email not verified',
            ]);
        }

        if ($user->role == 'user') {
            return view('errors.already-activated', [
                'message' => 'User already activated as a user',
            ]);
        }

        if ($user->role == 'admin') {
            return view('errors.admin-cannot-be-activated', [
                'message' => 'Admin cannot be activated as a user',
            ]);
        }

        $user->role = 'admin';
        $user->save();

        return view('success.user-verified', [
            'message' => 'User activated successfully',
            'user' => $user,
        ]);
    }

}
