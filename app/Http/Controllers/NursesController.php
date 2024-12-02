<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nurse;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class NursesController extends Controller
{
    // Posiblemente no se utilize para la aplicación
    public function index()
    {
        $nurses = Nurse::with('person')->get();

        if ($nurses->isEmpty()) {
            return response()->json([
                'msg' => 'No Nurses Found'
            ], 204);
        }

        return response()->json([
            'data' => $nurses
        ], 200);
    }
    //Posiblemente no se utilize para la aplicación
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'rfc' => 'required|string|unique:nurses,rfc',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $nurse = Nurse::create($request->all());

        if (!$nurse) {
            return response()->json([
                'msg' => 'Data not registered'
            ], 400);
        }

        return response()->json([
            'data' => $nurse
        ], 200);
    }
    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $nurse = Nurse::with('person')->find($id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Nurse Found'
            ], 404);
        }

        return response()->json([
            'msg' => 'Nurse Found Successfully',
            'data' => $nurse
        ]);
    }
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name_1' => 'required|string|max:255',
            'last_name_2' => 'nullable|string|max:255',
            'rfc' => [
                'required',
                'string',
                'max:13',
                'unique:nurses,rfc,' . $id,
                'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/'
            ]
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $nurse = Nurse::find($id);

        if (!$nurse) {
            return response()->json([
                'message' => 'No Data Found'
            ], 404);
        }

        $person = Person::find($nurse->person_id);

        if (!$person) {
            return response()->json([
                'message' => 'No Person Found'
            ], 404);
        }

        $person->name = $request->name;
        $person->last_name_1 = $request->last_name_1;
        $person->last_name_2 = $request->last_name_2;
        $person->save();

        $nurse->rfc = $request->rfc;
        $nurse->save();

        return response()->json([
            'message' => 'Nurse Updated Successfully',
        ], 200);
    }
    // Posiblemente no se utilize para la aplicación
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        $nurse = Nurse::find($id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Nurse Found'
            ], 404);
        }

        $person = Person::find($nurse->person_id);

        if (!$person) {
            return response()->json([
                'msg' => 'No Person Found'
            ], 404);
        }
        $person->delete();
        $nurse->delete();

        return response()->json([
            'msg' => 'Nurse Deleted Successfully'
        ], 200);
    }

    public function uploadImage(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }

        $user = auth()->user();

        $image = $request->file('image');

        if (!$user) {
            return response()->json([
                'message' => 'No User Found'
            ], 404);
        }

        $nurse = Nurse::find($user->id);

        if (!$nurse) {
            return response()->json([
                'message' => 'No Nurse Found'
            ], 404);
        }

        if ($nurse->image_path) {
            Storage::disk('s3')->delete($nurse->image_path);
        }

        $imagePath = Storage::disk('s3')->put('neocare/nurses_images', $image);

        $nurse->image_path = $imagePath;
        $nurse->save();

        return response()->json([
            'message' => 'Image Uploaded Successfully'
        ], 200);

    }

    public function destroyImage()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'No User Found'
            ], 404);
        }

        $nurse = Nurse::find($user->id);

        if (!$nurse) {
            return response()->json([
                'message' => 'No Nurse Found'
            ], 404);
        }

        if (!$nurse->image_path) {
            return response()->json([
                'message' => 'No Image Found'
            ], 404);
        }

        Storage::disk('s3')->delete($nurse->image_path);

        $nurse->image_path = null;
        $nurse->save();



        return response()->json([
            'message' => 'Image Deleted Successfully'
        ], 200);
    }
    public function viewImage()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'msg' => 'No User Found'
            ], 404);
        }

        $nurse = Nurse::find($user->id);

        if (!$nurse) {
            return response()->json([
                'msg' => 'No Nurse Found'
            ], 404);
        }

        if (!$nurse->image_path) {
            return response()->json([
                'msg' => 'No Image Found'
            ], 404);
        }

        try {
            $content = Storage::disk('s3')->get($nurse->image_path);
        } catch (FileNotFoundException $e) {
            return response()->json([
                'msg' => 'No Image Found in S3'
            ]);
        }
        return response($content, 200)->header('Content-Type', 'image/jpeg');
    }

}
