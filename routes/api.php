<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ApiAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('login', [ApiAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [ApiAuthController::class, 'logout']);

Route::prefix('register')->group(function () {
    Route::post('init', [RegistrationController::class, 'initializeRegistration']);
    Route::post('step1', [RegistrationController::class, 'submitStep1']);
    Route::post('step2', [RegistrationController::class, 'submitStep2']);
    Route::post('step3/send-verification', [RegistrationController::class, 'initiateEmailVerification']);
    Route::post('step3/verify', [RegistrationController::class, 'verifyEmail']);
    Route::post('step4', [RegistrationController::class, 'submitStep4']);
    Route::post('step5', [RegistrationController::class, 'submitStep5']);
    Route::get('resume', [RegistrationController::class, 'resumeRegistration']);
}); 