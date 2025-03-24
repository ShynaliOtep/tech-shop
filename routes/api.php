<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\BonusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/bonus/withdraw', [BonusController::class, 'requestWithdraw'])->middleware('auth');

Route::get('/cart', [CartController::class, 'cart']);

Route::post('/cart/items', [CartController::class, 'cartItems']);
