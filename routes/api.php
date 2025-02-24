<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BabiesController;
use App\Http\Controllers\BabiesDataController;
use App\Http\Controllers\BabyIncubatorsController;
use App\Http\Controllers\IncubatorsController;
use App\Http\Controllers\NursesController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\RelativesController;
use App\Http\Controllers\ChecksController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdafruitController;
use App\Http\Controllers\AddressesController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\UsersManagementController;
use App\Http\Controllers\RoomsController;

Route::prefix('v1')->group(function () {

    Route::prefix('sessions')->group(function () {
        Route::post('register-app', [SessionsController::class, 'registerApp']);
        Route::post('register-web', [SessionsController::class, 'registerWeb']);
        Route::post('login', [SessionsController::class, 'login']);
        Route::get('verify-email-web', [SessionsController::class, 'verifyEmailWeb'])->name('verify-email-web');
        Route::get('verify-email-app', [SessionsController::class, 'verifyEmailApp'])->name('verify-email-app');
        Route::post('resend-activation', [SessionsController::class, 'resend_activation']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('roleguest')->group(function () {
            Route::middleware('superadmin')->group(function () {
                Route::apiResource('rooms', RoomsController::class);
                Route::apiResource('addresses', AddressesController::class);
                Route::apiResource('hospitals', HospitalsController::class);
                Route::apiResource('babies', BabiesController::class);
                Route::post('add-person-relative/{baby_id}', [RelativesController::class, 'addPersonRelative']);
                Route::apiResource('babies-data', BabiesDataController::class);
                Route::apiResource('baby-incubators', BabyIncubatorsController::class);
                Route::apiResource('incubators', IncubatorsController::class);
                Route::get('incubators-nurses', [IncubatorsController::class, 'incubatorNurse']);
                Route::apiResource('nurses', NursesController::class);
                Route::apiResource('people', PeopleController::class);
                Route::apiResource('relatives', RelativesController::class);
                Route::apiResource('checks', ChecksController::class);
                Route::delete('profile-image-nurses', [NursesController::class, 'destroyImage']);
                Route::get('sessions/me', [SessionsController::class, 'me']);
                Route::get('sessions/role', [SessionsController::class, 'userRole']);
                Route::post('sessions/logout', [SessionsController::class, 'logout']);
                Route::get('nurse-checks', [ChecksController::class, 'checksByNurse']);
                Route::post('baby-to-incubator', [BabiesController::class, 'assignBabyToIncubator']);
                Route::post('crear-grupo', [AdafruitController::class, 'crearGrupo']);
                Route::prefix('users')->group(function () {
                    Route::put('role-management', [UsersManagementController::class, 'roleManagement']);
                    Route::get('/', [UsersManagementController::class, 'index']);
                    Route::get('/{id}', [UsersManagementController::class, 'show']);
                });
            });
            Route::post('profile-image-nurses', [NursesController::class, 'uploadImage']);
            Route::get('profile-image-nurses', [NursesController::class, 'viewImage']);
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