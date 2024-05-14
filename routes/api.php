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
use App\Http\Controllers\TelevisionController;
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

Route::get('/channel', [TelevisionController::class, 'channel']);

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
Route::post('/add_service_to_cart', [RoomServiceRequestController::class, 'add_to_cart']);
Route::get('/service_cart', [RoomServiceRequestController::class, 'show_cart']);
Route::delete('/delete_request/{item_id}', [RoomServiceRequestController::class, 'delete_request']);
Route::post('/request_service', [RoomServiceRequestController::class, 'request_service']);

// MENU
Route::get('/ads_lips', [MenuController::class, 'ads_lips_menu']);
Route::get('/menu_list', [MenuController::class, 'menu_list']);
Route::get('/menu_type', [MenuController::class, 'menu_type']);

//FOOD ORDER REQUEST
Route::post('/add_menu_to_cart', [FoodServiceRequestController::class, 'add_to_cart']);
Route::get('/menu_cart', [FoodServiceRequestController::class, 'show_cart']);
Route::delete('/delete_order/{item_id}', [FoodServiceRequestController::class, 'delete_order']);
Route::post('/food_order', [FoodServiceRequestController::class, 'food_order']);
Route::post('/payment_status', [FoodServiceRequestController::class, 'payment_status']);
Route::get('/payment_method', [FoodServiceRequestController::class, 'get_payment_method']);
Route::put('/payment_method', [FoodServiceRequestController::class, 'change_payment_method']);
Route::get('/show_qr_code', [FoodServiceRequestController::class, 'show_qr_code']);
Route::post('/notification', [FoodServiceRequestController::class, 'notification']);

Route::group(['middleware' => 'firebase'], function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change_password', [AuthController::class, 'change_password']);

    // CONTENT ADMIN
    Route::middleware('content_admin')->group(function () {
        Route::post('/greeting/{hotel_id}', [ContentController::class, 'greeting']);
        Route::get('/greeting/{hotel_id}', [ContentController::class, 'greeting_content']);
        Route::get('/hotel_about/{hotel_id}', [ContentController::class, 'hotel_about_data']);
        Route::post('/hotel_about/{hotel_id}', [ContentController::class, 'hotel_about']);
        Route::post('/hotel_photo/{hotel_id}', [ContentController::class, 'hotel_photo']);
        Route::post('/hotel_wifi/{hotel_id}', [ContentController::class, 'hotel_qr_code_wifi']);
        Route::post('/hotel_payment/{hotel_id}', [ContentController::class, 'hotel_qr_code_payment']);
        Route::post('/hotel_facilities/{hotel_id}', [ContentController::class, 'hotel_facilities_create']);
        Route::get('/hotel_facilities_list/{hotel_id}', [ContentController::class, 'hotel_facilities_list']);
        Route::get('/hotel_facilities/{facility_id}', [ContentController::class, 'hotel_facilities_data']);
        Route::put('/hotel_facilities/{facility_id}', [ContentController::class, 'hotel_facilities_update']);
        Route::post('/facility_image/{facility_id}', [ContentController::class, 'update_facility_image']);
        Route::delete('/hotel_facilities/{facility_id}', [ContentController::class, 'hotel_facilities_delete']);
        Route::post('/room_type/{hotel_id}', [ContentController::class, 'room_type_create']);
        Route::get('/room_type_list/{hotel_id}', [ContentController::class, 'room_type_list']);
        Route::get('/room_type_data/{room_id}', [ContentController::class, 'room_type_detail']);
        Route::put('/room_type/{room_id}', [ContentController::class, 'room_type_update']);
        Route::post('/room_type_image/{room_id}', [ContentController::class, 'update_room_image']);
        Route::delete('/room_type/{room_id}', [ContentController::class, 'room_type_delete']);
        Route::post('/amenities/{hotel_id}', [ContentController::class, 'amenities_create']);
        Route::get('/amenities_list/{hotel_id}', [ContentController::class, 'amenities_list']);
        Route::get('/amenities/{service_id}', [ContentController::class, 'amenities_data']);
        Route::put('/amenities/{service_id}', [ContentController::class, 'amenities_update']);
        Route::post('/amenity_image/{service_id}', [ContentController::class, 'update_amenity_image']);
        Route::delete('/amenities/{service_id}', [ContentController::class, 'amenities_delete']);
        Route::post('/ads_lips/{hotel_id}', [ContentController::class, 'ads_lips_menu']);
        Route::get('/ads_lips/{hotel_id}', [ContentController::class, 'ads_lips_content']);
        Route::post('/menu_type/{hotel_id}', [ContentController::class, 'menu_type_create']);
        Route::get('/menu_type/{hotel_id}', [ContentController::class, 'menu_type_list']);
        Route::get('/type/{hotel_id}', [ContentController::class, 'menu_type_dropdown']);
        Route::get('/menu_type_data/{menu_type_id}', [ContentController::class, 'menu_type_data']);
        Route::put('/menu_type/{menu_type_id}', [ContentController::class, 'menu_type_update']);
        Route::put('/menu_type_image/{menu_type_id}', [ContentController::class, 'update_menu_type_image']);
        Route::delete('/menu_type/{menu_type_id}', [ContentController::class, 'menu_type_delete']);
        Route::post('/menu/{hotel_id}', [ContentController::class, 'menu_create']);
        Route::get('/menu_list/{menu_type_id}', [ContentController::class, 'menu_list']);
        Route::get('/menu/{menu_id}', [ContentController::class, 'menu_data']);
        Route::put('/menu/{menu_id}', [ContentController::class, 'menu_update']);
        Route::post('/menu_image/{menu_id}', [ContentController::class, 'update_menu_image']);
        Route::delete('/menu/{menu_id}', [ContentController::class, 'menu_delete']);
    });

    // RECEPTIONIST
    Route::middleware('receptionist')->group(function () {
        Route::get('/room_number/{hotel_id}', [GuestController::class, 'room_number_list']);
        Route::get('/room_type/{hotel_id}', [GuestController::class, 'room_type_list']);
        Route::post('/guest', [GuestController::class, 'add_guest']);
        Route::get('/guest/{hotel_id}', [GuestController::class, 'guest_list']);
        Route::get('/guest_data/{room_number_id}', [GuestController::class, 'guest_data']);
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
        Route::get('/payment_status/{food_service_request_id}', [ServiceController::class, 'payment_status']);
        Route::post('/change_status/{food_service_rquest_id}', [ServiceController::class, 'change_status']);
        Route::get('/food_service_history/{hotel_id}', [ServiceController::class, 'food_service_history']);
        Route::get('/revenue/{hotel_id}', [ServiceController::class, 'revenue']);
    });

    // SUPERADMIN
    Route::middleware('super_admin')->group(function () {
        Route::post('/add_hotel', [ClientController::class, 'add_hotel']);
        Route::post('/add_admin/{hotel_id}', [ClientController::class, 'add_admin']);
        Route::post('/add_television/{hotel_id}', [ClientController::class, 'add_television']);
        Route::get('/hotel', [ClientController::class, 'hotel_data_list']);
        Route::get('/hotel_data/{hotel_id}', [ClientController::class, 'hotel_data']);
        Route::put('/update_hotel/{hotel_id}', [ClientController::class, 'update_hotel']);
        Route::post('/logo/{hotel_id}', [ClientController::class, 'update_hotel_logo']);
        Route::get('television_data/{hotel_id}', [ClientController::class, 'television_data']);
        Route::get('get_television/{television_id}', [ClientController::class, 'get_television']);
        Route::put('update_television/{television_id}', [ClientController::class, 'update_television']);
        Route::get('admin_list/{hotel_id}', [ClientController::class, 'admin_list']);
        Route::get('admin_data/{admin_id}', [ClientController::class, 'admin_data']);
        Route::put('update_admin/{admin_id}', [ClientController::class, 'update_admin']);
        Route::get('/hotel_list', [ClientController::class, 'hotel_list']);
        Route::get('/television_list/{hotel_id}', [ClientController::class, 'television_list']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
