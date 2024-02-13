<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MenuController;
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

// HOTEL
Route::get('/home', [HotelController::class, 'home']);
Route::get('/greeting', [HotelController::class, 'hotel_greeting']);
Route::get('/about', [HotelController::class, 'hotel_about']);
Route::get('/location', [HotelController::class, 'hotel_location']);
Route::get('/facilites', [HotelController::class, 'hotel_facilites']);

// ROOM
Route::get('/room_header', [RoomController::class, 'room_header']);
Route::get('/room_about', [RoomController::class, 'room_about']);
Route::get('/room_service', [RoomController::class, 'room_service']);

//MENU
Route::get('/menu_list', [MenuController::class, 'menu_list']);
Route::get('/qr_code', [MenuController::class, 'qr_code']);

// SUPERADMIN
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
