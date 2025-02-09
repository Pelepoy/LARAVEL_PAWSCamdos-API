<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DogController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * @Auth Routes
 */

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/v1/auth', function (Request $request) {
		return $request->user();
	});
	Route::post('/email-verification', [EmailVerificationController::class, 'email_verification']);
	Route::get('/resend-otp', [EmailVerificationController::class, 'resend_otp']);
	Route::post('/v1/logout', [AuthController::class, 'logout']);
});

/**
 * @Resource Routes
 */

Route::apiResource('/v1/dogs', DogController::class);
