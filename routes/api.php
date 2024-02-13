<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
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

Route::post('/content', [ContentController::class, 'add_content']);
Route::get('/content', [ContentController::class, 'show_content']);

Route::get('/greeting', [HotelController::class, 'hotel_greeting']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
