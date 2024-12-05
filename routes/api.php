<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BabiesController;
use App\Http\Controllers\BabiesDataController;
use App\Http\Controllers\BabyIncubatorsController;
use App\Http\Controllers\IncubatorsController;
use App\Http\Controllers\NursesController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\RelativesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ChecksController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\BabyNursesController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdafruitController;


Route::prefix('v1')->group(function () {

    Route::prefix('sessions')->group(function () {
        Route::post('register', [SessionsController::class, 'register']);
        Route::post('login', [SessionsController::class, 'login']);
        Route::get('verify-email', [SessionsController::class, 'verifyEmail'])->name('verify-email');
        Route::post('resend-activation', [SessionsController::class, 'resend_activation']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('role')->group(function () {
            Route::apiResource('babies', BabiesController::class);
            Route::apiResource('babies-data', BabiesDataController::class);
            Route::apiResource('baby-incubators', BabyIncubatorsController::class);
            Route::apiResource('incubators', IncubatorsController::class);
            Route::get('incubators-nurses', [IncubatorsController::class, 'incubatorNurse']);
            Route::apiResource('nurses', NursesController::class);
            Route::apiResource('people', PeopleController::class);
            Route::apiResource('relatives', RelativesController::class);
            Route::apiResource('notifications', NotificationsController::class);
            Route::apiResource('checks', ChecksController::class);
            Route::apiResource('schedules', SchedulesController::class);
            Route::apiResource('baby-nurses', BabyNursesController::class);
            Route::post('profile-image-nurses', [NursesController::class, 'uploadImage']);
            Route::get('profile-image-nurses', [NursesController::class, 'viewImage']);
            Route::delete('profile-image-nurses', [NursesController::class, 'destroyImage']);
            Route::get('sessions/me', [SessionsController::class, 'me']);
            Route::post('sessions/logout', [SessionsController::class, 'logout']);
            Route::get('nurse-checks', [ChecksController::class, 'checksByNurse']);
            Route::post('baby-to-incubator', [BabiesController::class, 'assignBabyToIncubator']);
        });

    });

    Route::get('nurse-activate/{id}', [SessionsController::class, 'activateNurse'])->name('nurse-activate')->where('id', '[0-9]+');

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

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello World'
    ]);
});