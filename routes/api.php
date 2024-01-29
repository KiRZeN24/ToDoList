<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\UserController as UserV1;
use App\Http\Controllers\api\v1\ToDoTaskController as TodoTaskV1;
use App\Http\Controllers\Api\LoginController;

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

Route::post('login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('v1/users', UserV1::class)->only(['index', 'store', 'update', 'destroy']);

    Route::apiResource('v1/todotask', TodoTaskV1::class)->only(['index', 'show', 'store', 'update', 'destroy']);
});
