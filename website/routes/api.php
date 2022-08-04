<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ApiController;
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

Route::get('/orders', [ApiController::class, 'listingOrder'])->middleware('auth.basic.once');
Route::post('/orders', [ApiController::class, 'saveOrder'])->middleware('auth.basic.once');
Route::delete('/orders', [ApiController::class, 'deleteOrder'])->middleware('auth.basic.once');

Route::get('/discount/{orderId}/', [ApiController::class, 'discountCalculator'])->middleware('auth.basic.once');