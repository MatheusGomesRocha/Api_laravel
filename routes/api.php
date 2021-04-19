<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

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

Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/user/{user}', [UserController::class, 'getUserLogin']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::post('/login', [UserController::class, 'login']);
Route::delete('/user/{user}/delete', [UserController::class, 'delete']);
Route::put('/user/{user}/update', [UserController::class, 'update']);
Route::post('/user/{user}/avatar', [UserController::class, 'updateAvatar']);
Route::get('/user/{user}/favorites', [UserController::class, 'getFavorites']);
Route::post('/user/{user}/setFavorites', [UserController::class, 'toFavorites']);
Route::delete('/user/favorites/remove/{id}', [UserController::class, 'removeFromFavorites']);

Route::post('/createProduct', [ProductController::class, 'createProduct']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/product/{id}', [ProductController::class, 'getOneProduct']);

Route::post('/cart/insertCart', [CartController::class, 'insertCart']);
Route::get('/cart/{id}', [CartController::class, 'getCart']);
Route::post('/cart/makeOrder', [CartController::class, 'makeOrder']);
Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart']);

Route::get('/orders', [OrderController::class, 'getOrder']);



