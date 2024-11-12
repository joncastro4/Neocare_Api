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

use App\Http\Controllers\BabyController;
use App\Http\Controllers\BabyDataController;
use App\Http\Controllers\BabyIncubatorController;
use App\Http\Controllers\IncubatorController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RelativeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BabyNurseController;
use App\Http\Controllers\SessionController;

Route::apiResource('/v1/babies', BabyController::class);

Route::apiResource('/v1/babies-data', BabyDataController::class);

Route::apiResource('/v1/baby-incubators', BabyIncubatorController::class);

Route::apiResource('/v1/incubators', IncubatorController::class);

Route::apiResource('/v1/nurses', NurseController::class);

Route::apiResource('/v1/people', PersonController::class);

Route::apiResource('/v1/relatives', RelativeController::class);

Route::apiResource('/v1/notifications', NotificationController::class);

Route::apiResource('/v1/checks', CheckController::class);

Route::apiResource('/v1/schedules', ScheduleController::class);

Route::apiResource('/v1/baby-nurses', BabyNurseController::class);

Route::post('/v1/sessions/register', [SessionController::class, 'register']);

Route::get('/v1/sessions/verify-email', [SessionController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verify-email');

Route::post('/v1/sessions/login', [SessionController::class, 'login']);

Route::post('/v1/sessions/resend-activation', [SessionController::class, 'resend_activation']);