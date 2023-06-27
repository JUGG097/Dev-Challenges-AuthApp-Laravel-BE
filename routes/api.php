<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/check', function (Request $request) {
    return Response::json(["success" => true], 200);
});

Route::prefix("v1/auth")->group(function () {
    Route::post("signup", [AuthController::class, "userSignUp"]);
    Route::post("login", [AuthController::class, "userLogin"]);
    Route::post("refreshToken", [AuthController::class, "refreshToken"]);
});

Route::middleware("auth:api")->prefix("v1/user")->group(function () {
    Route::get("profile", [UserController::class, "userDetails"]);
    Route::put("editProfile", [UserController::class, "userDetailsUpdate"]);
});
