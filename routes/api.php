<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * @Auth Routes
 */

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
Route::post('/v1/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/v1/auth', function (Request $request) {
		return $request->user();
	});
	Route::post('/email-verification', [EmailVerificationController::class, 'email_verification']);
	Route::get('/resend-otp', [EmailVerificationController::class, 'resend_otp']);
	Route::post('/v1/logout', [AuthController::class, 'logout']);
	Route::delete('/v1/pets/{pet}/force-delete', [PetController::class, 'forceDelete']);
});

/**
 * @Resource Routes
 */
Route::post('/v1/pets/qrcode', [PetController::class, 'qrCode']);
Route::get('/v1/pets/cursor-paginate', [PetController::class, 'petInfoCursorPaginate']);
Route::get('/v1/pets/all', [PetController::class, 'getAllPetInfo']);
Route::apiResource('/v1/pets', PetController::class);