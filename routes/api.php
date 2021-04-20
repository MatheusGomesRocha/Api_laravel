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

Route::post('/login', [UserController::class, 'login']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::get('/user/{userId}', [UserController::class, 'getUserLogin']);
Route::delete('/user/{userId}/delete', [UserController::class, 'delete']);
Route::put('/user/{userId}/update', [UserController::class, 'update']);
Route::post('/user/{userId}/avatar', [UserController::class, 'updateAvatar']);
Route::get('/user/{userId}/favorites', [UserController::class, 'getFavorites']);
Route::post('/user/{userId}/setFavorites', [UserController::class, 'toFavorites']);
Route::delete('/user/{userId}/favorites/remove', [UserController::class, 'removeFromFavorites']);
Route::get('/user/{userId}/address', [UserController::class, 'getAddress']);
Route::post('/user/{userId}/createAddress', [UserController::class, 'createAddress']);
Route::delete('/user/{userId}/address/remove', [UserController::class, 'removeAddress']);

Route::post('/createProduct', [ProductController::class, 'createProduct']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/product/{id}', [ProductController::class, 'getOneProduct']);

Route::post('/cart/insertCart', [CartController::class, 'insertCart']);
Route::get('/cart/{userId}', [CartController::class, 'getCart']);
Route::post('/cart/makeOrder/{userId}', [CartController::class, 'makeOrder']);
Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart']);

Route::get('/user/{userId}/orders', [OrderController::class, 'getOrder']);



