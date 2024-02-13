<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Hotel;
use App\Models\HotelFacilities;
use App\Models\Menu;
use App\Models\Room;
use App\Models\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function add_content(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'greeting' => 'required',
            'hotel_name' => 'required',
            'hotel_class' => 'required',
            // 'hotel_logo' => 'required',
            'hotel_about' => 'required',
            'hotel_check_in' => 'required',
            'hotel_check_out' => 'required',
            'hotel_photo' => 'required',
            'hotel_address' => 'required',
            'hotel_city' => 'required',
            'hotel_phone' => 'required',
            'facility_name' => 'required',
            'facility_image' => 'required',
            'facility_description' => 'required',
            'room_type' => 'required',
            'room_facility' => 'required',
            'room_description' => 'required',
            'room_image' => 'required',
            'room_television' => 'required',
            'room_service_name' => 'required',
            'room_service_image' => 'required',
            'menu_name' => 'required',
            'menu_description' => 'required',
            'menu_price' => 'required',
            'menu_rating' => 'required',
            'menu_image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        DB::beginTransaction();
        try {
            $file_name = time() . " - " . $request->hotel_photo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_photo = asset("uploads/registrations/" . $file_name);
            $request->hotel_photo->move(public_path('uploads/registrations/'), $file_name);

            $file_name = time() . " - " . $request->hotel_logo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_logo = asset("uploads/registrations/" . $file_name);
            $request->hotel_logo->move(public_path('uploads/registrations/'), $file_name);

            $hotel = Hotel::create([
                'name' => $request->hotel_name,
                'class' => $request->hotel_class,
                'check_in' => $request->hotel_check_in,
                'check_out' => $request->hotel_check_out,
                'greeting' => $request->greeting,
                'about' => $request->hotel_about,
                'photo' => $path_hotel_photo,
                'address' => $request->hotel_address,
                'city' => $request->hotel_city,
                'phone' => $request->hotel_phone,
                'logo' => $path_hotel_logo,
                'order_food_intro' => $request->order_food_intro,
                'qr_code_payment' => $request->qr_code_payment,
                'qr_code_wifi' => $request->qr_code_wifi,
            ]);

            $file_name = time() . " - " . $request->facility_image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_facility_image = asset("uploads/registrations/" . $file_name);
            $request->facility_image->move(public_path('uploads/registrations/'), $file_name);

            $hotel_facility = HotelFacilities::create([
                'hotel_id' => $hotel->id,
                'name' => $request->facility_name,
                'description' => $request->facility_description,
                'image' => $path_facility_image
            ]);

            $file_name = time() . " - " . $request->room_image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_room_image = asset("uploads/registrations/" . $file_name);
            $request->room_image->move(public_path('uploads/registrations/'), $file_name);

            $room = Room::create([
                'hotel_id' => $hotel->id,
                'type' => $request->room_type,
                'facility' => $request->room_facility,
                'description' => $request->room_description,
                'image' => $path_room_image,
                'television' => $request->room_television,
            ]);

            $file_name = time() . " - " . $request->room_service_image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_room_service_image = asset("uploads/registrations/" . $file_name);
            $request->room_service_image->move(public_path('uploads/registrations/'), $file_name);

            $room_service = RoomService::create([
                'room_id' => $room->id,
                'name' => $request->room_service_name,
                'image' => $path_room_service_image,
            ]);

            $file_name = time() . " - " . $request->menu_image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_menu_image = asset("uploads/registrations/" . $file_name);
            $request->menu_image->move(public_path('uploads/registrations/'), $file_name);

            $menu = Menu::create([
                'hotel_id' => $hotel->id,
                'name' => $request->menu_name,
                'description' => $request->menu_description,
                'price' => $request->menu_price,
                'rating' => $request->menu_rating,
                'image' => $path_menu_image,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to save data',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'Data saved successfully!',
            'hotel' => $hotel,
            'hotel_facility' => $hotel_facility,
            'room' => $room,
            'room_service' => $room_service,
            'menu' => $menu,
        ]);
    }

    public function show_content()
    {
        return response()->json(Content::all());
    }
}
