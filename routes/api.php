<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
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

Route::prefix('v1')->group(function () {

    Route::prefix('sessions')->group(function () {
        Route::post('register', [SessionsController::class, 'register']);
        Route::get('verify-email', [SessionsController::class, 'verifyEmail'])->middleware('signed')->name('verify-email');
        Route::post('login', [SessionsController::class, 'login']);
        Route::post('resend-activation', [SessionsController::class, 'resend_activation']);
    });

    Route::apiResource('babies', BabiesController::class);
    Route::apiResource('babies-data', BabiesDataController::class);
    Route::apiResource('baby-incubators', BabyIncubatorsController::class);
    Route::apiResource('incubators', IncubatorsController::class);
    Route::apiResource('nurses', NursesController::class);
    Route::apiResource('people', PeopleController::class);
    Route::apiResource('relatives', RelativesController::class);
    Route::apiResource('notifications', NotificationsController::class);
    Route::apiResource('checks', ChecksController::class);
    Route::apiResource('schedules', SchedulesController::class);
    Route::apiResource('baby-nurses', BabyNursesController::class);
});

// Ruta de prueba
Route::get('/pruebaapi', function () {
    return view('welcome');
});