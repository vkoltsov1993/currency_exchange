<?php

use App\Http\Controllers\ExchangeRequestController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('store/{userId}', [ExchangeRequestController::class, 'store']);
Route::get('exchange-requests', [ExchangeRequestController::class, 'list']);
Route::post('apply/{userId}', [ExchangeRequestController::class, 'apply']);
