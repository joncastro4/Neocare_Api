<?php

use App\Http\Controllers\DataController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BabiesController;
use App\Http\Controllers\BabiesDataController;
use App\Http\Controllers\IncubatorsController;
use App\Http\Controllers\NursesController;
use App\Http\Controllers\RelativesController;
use App\Http\Controllers\ChecksController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdafruitController;
use App\Http\Controllers\AddressesController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\UsersManagementController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\ProfileController;

Route::prefix('v1')->group(function () {

    Route::prefix('sessions')->group(function () {
        Route::post('register-app', [SessionsController::class, 'registerApp']);
        Route::post('register-web', [SessionsController::class, 'registerWeb']);
        Route::post('login/app', [SessionsController::class, 'loginApp']);
        Route::post('login/web', [SessionsController::class, 'loginWeb']);
        Route::get('verify-email-web', [SessionsController::class, 'verifyEmailWeb'])->name('verify-email-web');
        Route::get('verify-email-app', [SessionsController::class, 'verifyEmailApp'])->name('verify-email-app');
        Route::post('resend-activation', [SessionsController::class, 'resend_activation']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('roleguest')->group(function () {
            Route::apiResource('checks', ChecksController::class);
            Route::apiResource('rooms', RoomsController::class);
            Route::apiResource('incubators', IncubatorsController::class);
            Route::apiResource('nurses', NursesController::class);
            Route::post('baby-to-incubator', [BabiesController::class, 'assignBabyToIncubator']);
            Route::middleware('superadmin')->group(function () {
                Route::middleware('nurseadmin')->group(function () {
                    Route::apiResource('babies', BabiesController::class);
                    Route::get('babiesNoPaginate', [BabiesController::class, 'indexNoPaginate']);
                    Route::apiResource('babies-data', BabiesDataController::class);
                    Route::apiResource('relatives', RelativesController::class);
                });
                Route::apiResource('addresses', AddressesController::class);
                Route::get('addressesNoPaginate', [AddressesController::class, 'indexNoPaginate']);
                Route::apiResource('hospitals', HospitalsController::class);
                Route::get('hospitalsNoPaginate', [HospitalsController::class, 'indexNoPaginate']);
                Route::get('incubators-nurses', [IncubatorsController::class, 'incubatorNurse']);
                Route::delete('profile-image-nurses', [NursesController::class, 'destroyImage']);
                Route::get('sessions/role', [SessionsController::class, 'userRole']);
                Route::post('sessions/logout', [SessionsController::class, 'logout']);
                Route::post('crear-grupo', [AdafruitController::class, 'crearGrupo']);
                Route::prefix('users')->group(function () {
                    Route::put('role-management', [UsersManagementController::class, 'roleManagement']);
                    Route::get('/', [UsersManagementController::class, 'index']);
                    Route::get('/{id}', [UsersManagementController::class, 'show']);
                });
            });
            Route::prefix('profile')->group(function () {
                Route::get('me', [ProfileController::class, 'me']);
                Route::put('/', [ProfileController::class, 'update']);
                Route::delete('/', [ProfileController::class, 'destroy']);
                Route::post('upload-image', [NursesController::class, 'uploadImage']);
                Route::get('view-image', [NursesController::class, 'viewImage']);
            });
        });
    });

    Route::get('nurse-activate/{id}', [SessionsController::class, 'activateNurse'])->name('nurse-activate')->where('id', '[0-9]+');
    Route::get('user-activate/{id}', [SessionsController::class, 'activateUser'])->name('user-activate')->where('id', '[0-9]+');

    Route::get('/bpm', [AdafruitController::class, 'bpm']);
    Route::get('/fotoresistencia', [AdafruitController::class, 'fotoresistencia']);
    Route::get('/humedad', [AdafruitController::class, 'humedad']);
    Route::get('/oxigeno', [AdafruitController::class, 'oxigeno']);
    Route::get('/rgb', [AdafruitController::class, 'rgb']);
    Route::get('/temperaturacorporal', [AdafruitController::class, 'temperaturacorporal']);
    Route::get('/temperaturambiental', [AdafruitController::class, 'temperaturambiental']);
    Route::get('/vibraciones', [AdafruitController::class, 'vibraciones']);
    // Ruta para obtener todos los sensores
    Route::get('/', [AdafruitController::class, 'obtenerTodosLosSensores']);
    Route::get('/sensores', [AdafruitController::class, 'obtenerTodosLosSensores']);
});

Route::prefix('v2')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('data-sensors', DataController::class);
        Route::get('data-sensors-by-type/{type}', [DataController::class, 'getByType']);
    });
});

Route::get('/operaciones', function () {
    return response()->json([
        'operaciones' => [
            'suma',
            'resta',
            'multiplicacion',
            'division'
        ]
    ]);
});

Route::get('/test-mongo', function () {
    try {
        DB::connection('mongodb')->getMongoClient()->listDatabases();
        return "Conexión a MongoDB exitosa!";
    } catch (\Exception $e) {
        return "Error al conectar a MongoDB: " . $e->getMessage();
    }
});