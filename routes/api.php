<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceController;
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

// Route::post('/content', [ContentController::class, 'add_content']);
// Route::get('/content', [ContentController::class, 'show_content']);

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

// ROOM SERVICE REQUEST


// MENU
Route::get('/menu_list', [MenuController::class, 'menu_list']);
Route::get('/qr_code', [MenuController::class, 'qr_code']);

//FOOD ORDER REQUEST


// CONTENT ADMIN
Route::post('/greeting/{hotel_id}', [ContentController::class, 'greeting']);
Route::get('/hotel_about/{hotel_id}', [ContentController::class, 'hotel_name']);
Route::post('/hotel_about/{hotel_id}', [ContentController::class, 'hotel_about']);
Route::post('/hotel_facilities/{hotel_id}', [ContentController::class, 'hotel_facilities_create']);
Route::put('/hotel_facilities/{facility_id}', [ContentController::class, 'hotel_facilities_update']);
Route::delete('/hotel_facilities/{facility_id}', [ContentController::class, 'hotel_facilities_delete']);
Route::post('/room_about/{hotel_id}', [ContentController::class, 'room_about_create']);
Route::put('/room_about/{room_id}', [ContentController::class, 'room_about_update']);
Route::post('/amenities/{hotel_id}', [ContentController::class, 'amenities_create']);
Route::put('/amenities/{service_id}', [ContentController::class, 'amenities_update']);
Route::delete('/amenities/{service_id}', [ContentController::class, 'amenities_delete']);
Route::post('/menu/{hotel_id}', [ContentController::class, 'menu_create']);
Route::put('/menu/{menu_id}', [ContentController::class, 'menu_update']);
Route::delete('/menu/{menu_id}', [ContentController::class, 'menu_delete']);

// RECEPTIONIST
Route::get('/room_list/{hotel_id}', [GuestController::class, 'room_list']);
Route::post('/guest/{room_number_id}', [GuestController::class, 'guest']);

// SERVICE ADMIN
Route::get('/room_service_list/{hotel_id}', [ServiceController::class, 'room_service_list']);
Route::get('/room_service_detail/{room_service_request_id}', [ServiceController::class, 'room_service_detail']);
Route::get('/food_service_list/{hotel_id}', [ServiceController::class, 'food_service_list']);
Route::get('/food_service_detail/{menu_service_request_id}', [ServiceController::class, 'food_service_detail']);

// SUPERADMIN
Route::post('/login', [AuthController::class, 'login']);
Route::post('/add_hotel', [ClientController::class, 'add_hotel']);
Route::post('/add_admin', [ClientController::class, 'add_admin']);
Route::post('/add_television/{hotel_id}', [ClientController::class, 'add_television']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
