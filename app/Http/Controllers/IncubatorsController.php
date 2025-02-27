<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BabyIncubator;
use Illuminate\Http\Request;
use App\Models\Incubator;
use Illuminate\Support\Facades\Validator;
use App\Models\UserPerson;
use App\Models\Nurse;
use Illuminate\Support\Facades\Http;

class IncubatorsController extends Controller
{
    public $incubatorNotFound = 'No Incubator Found';

    public function index(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'hospital_id' => 'required|integer|exists:hospitals,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $incubators = Incubator::with('room', 'baby_incubator.baby.person', 'baby_incubator.nurse.userPerson.person')
            ->whereHas('room', function ($query) use ($request) {
                if ($request->room_id) {
                    $query->where('room_id', $request->room_id);
                }
                $query->where('hospital_id', $request->hospital_id);
            });

        if ($user->role == 'nurse') {
            $userPerson = UserPerson::where('user_id', $user->id)->first();
            $nurse = Nurse::where('user_person_id', $userPerson->id)->first();

            $incubators->whereHas('baby_incubator', function ($query) use ($nurse) {
                $query->where('nurse_id', $nurse->id);
            });
        }

        $incubators = $incubators->orderByDesc('created_at')->get();

        if ($incubators->isEmpty()) {
            return response()->json([
                'msg' => 'No Incubators Found'
            ], 404);
        }

        $data = $incubators->map(function ($incubator) use ($user) {
            $nurseFullName = null;
            if (!($user->role == 'nurse')) {
                $nurseFullName = $incubator->baby_incubator->first()->nurse->userPerson->person->name . ' ' . $incubator->baby_incubator->first()->nurse->userPerson->person->last_name_1 . ' ' . $incubator->baby_incubator->first()->nurse->userPerson->person->last_name_2 ?? 'No Nurse';
            }
            $babyFullName = $incubator->baby_incubator->first()->baby->person->name . ' ' . $incubator->baby_incubator->first()->baby->person->last_name_1 . ' ' . $incubator->baby_incubator->first()->baby->person->last_name_2 ?? 'No Baby';
            return [
                'id' => $incubator->id,
                'state' => $incubator->state,
                'room_number' => $incubator->room->number,
                'nurse' => $nurseFullName,
                'baby' => $babyFullName,
                'created_at' => $incubator->created_at
            ];
        });

        return response()->json([
            'incubators' => $data
        ], 200);
    }
    public function incubatorNurse()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'No user found'
            ], 404);
        }

        // Si el usuario no es admin, obtenemos todas las incubadoras sin importar la relación con la enfermera
        if ($user->role != 'admin') {
            $incubators = Incubator::orderByDesc('created_at')->get();

            if ($incubators->isEmpty()) {
                return response()->json([
                    'msg' => 'No incubators found'
                ], 404);
            }

            // Agrega el estado de cada incubadora al arreglo
            $incubatorsWithState = $incubators->map(function ($incubator) {
                $incubatorDetails = Incubator::find($incubator->id);
                $incubator->state = $incubatorDetails ? $incubatorDetails->state : 'Unknown';
                return $incubator;
            });

            return response()->json([
                'data' => $incubatorsWithState
            ], 200);
        }

        // Si el usuario es admin, obtenemos todas las incubadoras sin filtro
        $incubators = Incubator::orderByDesc('created_at')->get();

        if ($incubators->isEmpty()) {
            return response()->json([
                'msg' => 'No incubators found'
            ], 204);
        }

        // Agrega el estado de cada incubadora al arreglo
        $incubatorsWithState = $incubators->map(function ($incubator) {
            $incubatorDetails = Incubator::find($incubator->id);
            $incubator->state = $incubatorDetails ? $incubatorDetails->state : 'Unknown';
            return $incubator;
        });

        return response()->json([
            'data' => $incubatorsWithState
        ], 200);
    }


    public function store()
    {
        $incubator = new Incubator();
        $incubator->save();

        $groupName = 'incubator' . $incubator->id;
        $groupData = [
            'name' => $groupName,
            'description' => 'Incubator ' . $incubator->id,
        ];

        try {
            $response = Http::withHeaders([
                'X-AIO-Key' => "aio_nBRg95EbrYiAnrK6jxq89C2bTHXH",
            ])->post("https://io.adafruit.com/api/v2/Tunas/groups", $groupData);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Error al crear el grupo.',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo crear el grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'msg' => 'Incubadora Agregada Correctamente',
            'data' => $incubator
        ], 201);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        return response()->json([
            'data' => $incubator
        ], 200);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $validate = Validator::make($request->all(), [
            'state' => 'required|string|in:active,available,inactive',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $BabyIncubator = BabyIncubator::find($id);

        if (!$BabyIncubator) {
            return response()->json([
                'msg' => 'No se encontraron los datos'
            ], 404);
        }

        $incubator = Incubator::find($BabyIncubator->incubator_id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->state = $request->state;
        $incubator->save();

        return response()->json([
            'data' => $incubator
        ], 200);
    }
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $incubator = Incubator::find($id);

        if (!$incubator) {
            return response()->json([
                'msg' => $this->incubatorNotFound
            ], 404);
        }

        $incubator->delete();

        return response()->json([
            'msg' => 'Incubator Deleted'
        ], 200);
    }
}
