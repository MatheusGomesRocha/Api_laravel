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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function (Request $request) {
    return ['pong'=>true];
});

Route::get('/users', [\App\Http\Controllers\UserController::class, 'getUsers']);
Route::get('/user/{user}', [\App\Http\Controllers\UserController::class, 'getUserLogin']);
Route::post('/registerUser', [\App\Http\Controllers\UserController::class, 'registerUser']);
Route::post('/login', [\App\Http\Controllers\UserController::class, 'login']);
Route::delete('/user/delete/{user}', [\App\Http\Controllers\UserController::class, 'delete']);
Route::put('/user/update/{user}', [\App\Http\Controllers\UserController::class, 'update']);
Route::post('/user/avatar', [\App\Http\Controllers\UserController::class, 'updateAvatar']);

Route::post('/createProduct', [\App\Http\Controllers\ProductController::class, 'createProduct']);
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'getProducts']);
Route::get('/product/{id}', [\App\Http\Controllers\ProductController::class, 'getOneProduct']);


