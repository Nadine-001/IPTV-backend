<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\FoodServiceRequestController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomServiceRequestController;
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

Route::post('/sign_up', [AuthController::class, 'sign_up']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);

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
Route::post('/add_service_to_cart/{service_id}', [RoomServiceRequestController::class, 'add_to_cart']);
Route::get('/service_cart', [RoomServiceRequestController::class, 'show_cart']);
Route::put('/increase_item_request/{item_id}', [RoomServiceRequestController::class, 'increase_item']);
Route::put('/decrease_item_request/{item_id}', [RoomServiceRequestController::class, 'decrease_item']);
Route::delete('/delete_request/{item_id}', [RoomServiceRequestController::class, 'delete_request']);
Route::post('/request_service', [RoomServiceRequestController::class, 'request_service']);

// MENU
Route::get('/ads_lips', [MenuController::class, 'ads_lips_menu']);
Route::get('/menu_list', [MenuController::class, 'menu_list']);
Route::get('/qr_code', [MenuController::class, 'qr_code']);

//FOOD ORDER REQUEST
Route::post('/add_menu_to_cart/{menu_id}', [FoodServiceRequestController::class, 'add_to_cart']);
Route::get('/menu_cart', [FoodServiceRequestController::class, 'show_cart']);
Route::put('/increase_item_order/{item_id}', [FoodServiceRequestController::class, 'increase_item']);
Route::put('/decrease_item_order/{item_id}', [FoodServiceRequestController::class, 'decrease_item']);
Route::delete('/delete_order/{item_id}', [FoodServiceRequestController::class, 'delete_order']);
Route::post('/food_order', [FoodServiceRequestController::class, 'food_order']);

Route::group(['middleware' => 'firebase'], function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change_password', [AuthController::class, 'change_password']);

    // CONTENT ADMIN
    Route::middleware('content_admin')->group(function () {
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
        Route::post('/ads_lips/{hotel_id}', [ContentController::class, 'ads_lips_menu']);
        Route::post('/menu/{hotel_id}', [ContentController::class, 'menu_create']);
        Route::put('/menu/{menu_id}', [ContentController::class, 'menu_update']);
        Route::delete('/menu/{menu_id}', [ContentController::class, 'menu_delete']);
    });

    // RECEPTIONIST
    Route::middleware('receptionist')->group(function () {
        Route::get('/room_number/{hotel_id}', [GuestController::class, 'room_number_list']);
        Route::get('/room_type/{hotel_id}', [GuestController::class, 'room_type_list']);
        Route::post('/guest', [GuestController::class, 'add_guest']);
        Route::get('/guest/{hotel_id}', [GuestController::class, 'guest_list']);
        Route::put('/guest/{room_number_id}', [GuestController::class, 'update_guest']);
        Route::delete('/guest/{room_number_id}', [GuestController::class, 'delete_guest']);
    });

    // SERVICE ADMIN
    Route::middleware('service_admin')->group(function () {
        Route::get('/room_service_list/{hotel_id}', [ServiceController::class, 'room_service_list']);
        Route::get('/room_service_detail/{room_service_request_id}', [ServiceController::class, 'room_service_detail']);
        Route::post('/accept_service_request/{room_service_request_id}', [ServiceController::class, 'accept_service_request']);
        Route::post('/decline_service_request/{room_service_request_id}', [ServiceController::class, 'decline_service_request']);
        Route::get('/room_service_history/{hotel_id}', [ServiceController::class, 'room_service_history']);
        Route::get('/food_service_list/{hotel_id}', [ServiceController::class, 'food_service_list']);
        Route::get('/food_service_detail/{food_service_request_id}', [ServiceController::class, 'food_service_detail']);
        Route::post('/accept_food_order/{food_service_request_id}', [ServiceController::class, 'accept_food_order']);
        Route::post('/decline_food_order/{food_service_request_id}', [ServiceController::class, 'decline_food_order']);
        Route::get('/food_service_history/{hotel_id}', [ServiceController::class, 'food_service_history']);
    });

    // SUPERADMIN
    // Route::middleware('super_admin')->group(function () {
        Route::post('/add_hotel', [ClientController::class, 'add_hotel']);
        Route::post('/add_admin', [ClientController::class, 'add_admin']);
        Route::post('/add_television/{hotel_id}', [ClientController::class, 'add_television']);
        Route::get('/hotel/{hotel_id}', [ClientController::class, 'hotel_data_list']);
        Route::get('/hotel_data/{hotel_id}', [ClientController::class, 'hotel_data']);
        Route::put('/update_hotel/{hotel_id}', [ClientController::class, 'update_hotel']);
        Route::get('television_data/{hotel_id}', [ClientController::class, 'television_data']);
        Route::put('update_television/{television_id}', [ClientController::class, 'update_television']);
        Route::get('admin_list/{hotel_id}', [ClientController::class, 'admin_list']);
        Route::put('update_admin/{admin_id}', [ClientController::class, 'update_admin']);
        Route::get('/hotel_list/{hotel_id}', [ClientController::class, 'hotel_list']);
        Route::get('/television_list/{hotel_id}', [ClientController::class, 'television_list']);
    // });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
