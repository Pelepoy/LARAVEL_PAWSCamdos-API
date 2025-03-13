<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * @Admin Routes
 */
Route::prefix('/v1/admin')->group(function () {
	Route::get('/dashboard', [AdminController::class, 'dashboard']);
	Route::get('/users', [AdminController::class, 'getAllUsers']);
	Route::get('/pets', [AdminController::class, 'getAllPets']);
	Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
	Route::delete('/pets/{id}', [AdminController::class, 'softDeletePet']);
	Route::post('/pets/restore/{id}', [AdminController::class, 'restorePetInfo']);
	Route::delete('/pets/force-delete/{id}', [AdminController::class, 'forceDeletePet']);
});

/**
 * @Auth Routes
 */
Route::prefix('/v1')->group(function () {
	Route::post('/register', [AuthController::class, 'register']);
	Route::post('/login', [AuthController::class, 'login']);
	Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset'); // Bypass laravel password class
	Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


/** 
 *  @Registered Sanctum Routes 
 */
Route::middleware('auth:sanctum')->group(function () {
	Route::get('/v1/auth', function (Request $request) {
		return $request->user();
	});
	Route::post('/v1/email-verification', [EmailVerificationController::class, 'email_verification']);
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
Route::apiResource('/v1/pets', PetController::class); // Implement sanctum thru controller