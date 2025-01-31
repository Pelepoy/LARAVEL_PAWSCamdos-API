<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DogController;
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
	Route::post('/v1/logout', [AuthController::class, 'logout']);
});

/**
 * @Resource Routes
 */

Route::apiResource('/v1/dogs-info', DogController::class);