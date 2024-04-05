<?php

namespace App\Http\Controllers;

// use App\Models\Content;
use App\Models\Hotel;
use App\Models\HotelFacilities;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\Room;
use App\Models\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function greeting(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'greeting' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $hotel = Hotel::where('id', intval($hotel_id))->first();

        try {
            $hotel->update([
                'greeting' => $request->greeting,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save greeting content',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('greeting content added succesfully');
    }

    public function greeting_content($hotel_id)
    {
        $hotel = Hotel::where('id', intval($hotel_id))->first();

        try {
            $hotel_greeting = $hotel->greeting;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get greeting content',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'greeting' => $hotel_greeting
        ]);
    }

    public function hotel_about_data($hotel_id)
    {
        $hotel = Hotel::where('id', intval($hotel_id))->first();

        try {
            $hotel_name = $hotel->name;
            $hotel_class = $hotel->class;
            $hotel_about = $hotel->about;
            $hotel_check_in = $hotel->check_in;
            $hotel_check_out = $hotel->check_out;
            $hotel_photo = $hotel->photo;
            $hotel_qr_code_wifi = $hotel->qr_code_wifi;
            $hotel_qr_code_payment = $hotel->qr_code_payment;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_name' => $hotel_name,
            'hotel_class' => $hotel_class,
            'hotel_about' => $hotel_about,
            'hotel_check_in' => $hotel_check_in,
            'hotel_check_out' => $hotel_check_out,
            'hotel_photo' => $hotel_photo,
            'hotel_qr_code_wifi' => $hotel_qr_code_wifi,
            'hotel_qr_code_payment' => $hotel_qr_code_payment,
        ]);
    }

    public function hotel_about(Request $request, $hotel_id)
    {
        $hotel = Hotel::where('id', intval($hotel_id))->first();

        $validator = Validator::make($request->all(), [
            'hotel_class' => 'required',
            'hotel_about' => 'required',
            'hotel_check_in' => 'required',
            'hotel_check_out' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $hotel->update([
                'class' => $request->hotel_class,
                'about' => $request->hotel_about,
                'check_in' => $request->hotel_check_in,
                'check_out' => $request->hotel_check_out,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel info',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel info added succesfully');
    }

    public function hotel_photo(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'hotel_photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $file_name = time() . " - " . $request->hotel_photo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_photo = asset("uploads/hotel_photo/" . $file_name);
            $request->hotel_photo->move(public_path('uploads/hotel_photo/'), $file_name);

            $hotel->update([
                'photo' => $path_hotel_photo,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel photo',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel photo saved succesfully');
    }

    public function hotel_qr_code_wifi(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'hotel_qr_code_wifi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $file_name = time() . " - " . $request->hotel_qr_code_wifi->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_qr_code_wifi = asset("uploads/hotel_qr_code_wifi/" . $file_name);
            $request->hotel_qr_code_wifi->move(public_path('uploads/hotel_qr_code_wifi/'), $file_name);

            $hotel->update([
                'qr_code_wifi' => $path_hotel_qr_code_wifi,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel QR code wifi',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel QR code wifi saved succesfully');
    }

    public function hotel_qr_code_payment(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'hotel_qr_code_payment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $file_name = time() . " - " . $request->hotel_qr_code_payment->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_qr_code_payment = asset("uploads/hotel_qr_code_payment/" . $file_name);
            $request->hotel_qr_code_payment->move(public_path('uploads/hotel_qr_code_payment/'), $file_name);

            $hotel->update([
                'qr_code_payment' => $path_hotel_qr_code_payment,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel QR code payment',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel QR code payment saved succesfully');
    }

    public function hotel_facilities_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            HotelFacilities::create([
                'hotel_id' => $hotel_id,
                'name' => $request->name,
                'description' => $request->description,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel facilities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel facilities added succesfully');
    }

    public function hotel_facilities_data(Request $request, $facility_id)
    {
        $facility = HotelFacilities::where('id', $facility_id)->first();

        try {
            $facility_id = $facility->id;
            $facility_name = $facility->name;
            $facility_description = $facility->description;
            $facility_image = $facility->image;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel facilities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'facility_id' => $facility_id,
            'facility_name' => $facility_name,
            'facility_description' => $facility_description,
            'facility_image' => $facility_image,
        ]);
    }

    public function hotel_facilities_update(Request $request, $facility_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $facility = HotelFacilities::where('id', $facility_id)->first();

        try {
            $facility->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update hotel facilities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel facilities updated succesfully');
    }

    public function update_facility_image(Request $request, $facility_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $facility = HotelFacilities::where('id', $facility_id)->first();

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/hotel_facilities/" . $file_name);
            $request->image->move(public_path('uploads/hotel_facilities/'), $file_name);

            $facility->update([
                'image' => $path_image
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update image facility',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('image facility updated succesfully');
    }

    public function hotel_facilities_delete($facility_id)
    {
        $facility = HotelFacilities::where('id', intval($facility_id))->first();
        $deleted = $facility->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed to delete hotel facility"
            ], 400);
        }

        return response()->json([
            "message" => "hotel facility deleted successfully"
        ]);
    }

    public function room_type_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'facility' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/about/" . $file_name);
            $request->image->move(public_path('uploads/about/'), $file_name);

            Room::create([
                'hotel_id' => $hotel_id,
                'type' => $request->type,
                'facility' => $request->facility,
                'description' => $request->description,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save room about',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('room about added succesfully');
    }

    public function room_type_list($hotel_id)
    {
        $rooms = Room::where('hotel_id', $hotel_id)->get();

        try {
            $room_type = [];
            foreach ($rooms as $room) {
                $type = $room->type;
                $image = $room->image;

                $room_type[] = [
                    'id' => $room->id,
                    'type' => $type,
                    'image' => $image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_type' => $room_type
        ]);
    }

    public function room_type_detail($room_id)
    {
        $room = Room::where('id', $room_id)->first();

        try {
            $type = $room->type;
            $facility = $room->facility;
            $description = $room->description;
            $image = $room->image;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'type' => $type,
            'facility' => $facility,
            'description' => $description,
            'image' => $image,
        ]);
    }

    public function room_type_update(Request $request, $room_id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'facility' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $room = Room::where('id', $room_id)->first();

        try {
            $room->update([
                'type' => $request->type,
                'facility' => $request->facility,
                'description' => $request->description,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update room about',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('room about updated succesfully');
    }

    public function update_room_image(Request $request, $room_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $room = Room::where('id', $room_id)->first();

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/about/" . $file_name);
            $request->image->move(public_path('uploads/about/'), $file_name);

            $room->update([
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update room image',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('room image updated succesfully');
    }

    public function room_type_delete($room_id)
    {
        $room = Room::where('id', $room_id)->first();
        $deleted = $room->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed to delete room type"
            ], 400);
        }

        return response()->json([
            "message" => "room type deleted successfully"
        ]);
    }

    public function amenities_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/room_services/" . $file_name);
            $request->image->move(public_path('uploads/room_services/'), $file_name);

            RoomService::create([
                'hotel_id' => $hotel_id,
                'name' => $request->name,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save amenities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('amenities added succesfully');
    }

    public function amenities_data($service_id)
    {
        $service = RoomService::where('id', intval($service_id))->first();

        try {
            $service_id = $service->id;
            $service_name = $service->name;
            $service_image = $service->image;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get amenities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'service_id' => $service_id,
            'service_name' => $service_name,
            'service_image' => $service_image,
        ]);
    }

    public function amenities_update(Request $request, $service_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $service = RoomService::where('id', intval($service_id))->first();

        try {
            $service->update([
                'name' => $request->name,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update amenities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('amenities updated succesfully');
    }

    public function update_amenity_image(Request $request, $service_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $service = RoomService::where('id', intval($service_id))->first();

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/room_services/" . $file_name);
            $request->image->move(public_path('uploads/room_services/'), $file_name);

            $service->update([
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update amenity image',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('amenity image updated succesfully');
    }

    public function amenities_delete(Request $request, $service_id)
    {
        $service = RoomService::where('id', intval($service_id))->first();
        $deleted = $service->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed to delete room service"
            ], 400);
        }

        return response()->json([
            "message" => "room service deleted successfully"
        ]);
    }

    public function ads_lips_menu(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'ads_lips' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $hotel->update([
                'order_food_intro' => $request->ads_lips
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save ads lips menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('ads lips menu added succesfully');
    }

    public function ads_lips_content($hotel_id)
    {
        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $ads_lips = $hotel->order_food_intro;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get ads lips menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'ads_lips' => $ads_lips
        ]);
    }

    public function menu_type_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/menu_type/" . $file_name);
            $request->image->move(public_path('uploads/menu_type/'), $file_name);

            MenuType::create([
                'hotel_id' => $hotel_id,
                'type' => $request->type,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save menu type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu type added succesfully');
    }

    public function menu_type_list($hotel_id)
    {
        $menu_types = MenuType::where('hotel_id', $hotel_id)->get();

        try {
            $menu_type = [];
            foreach ($menu_types as $types) {
                $type = $types->type;
                $image = $types->image;

                $menu_type[] = [
                    'id' => $types->id,
                    'type' => $type,
                    'image' => $image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get menu type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'menu_type' => $menu_type
        ]);
    }

    public function menu_type_update(Request $request, $menu_type_id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $menu_types = MenuType::where('id', $menu_type_id)->first();

        try {
            $menu_types->update([
                'type' => $request->type,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update menu type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu type updated succesfully');
    }

    public function update_menu_type_image(Request $request, $menu_type_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $menu_types = MenuType::where('id', $menu_type_id)->first();

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/menu_type/" . $file_name);
            $request->image->move(public_path('uploads/menu_type/'), $file_name);

            $menu_types->update([
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update menu type',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu type updated succesfully');
    }

    public function menu_type_delete($menu_type_id)
    {
        $menu_types = MenuType::where('id', $menu_type_id)->first();
        $deleted = $menu_types->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed to delete menu type"
            ], 400);
        }

        return response()->json([
            "message" => "menu type deleted successfully"
        ]);
    }

    public function menu_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/menu/" . $file_name);
            $request->image->move(public_path('uploads/menu/'), $file_name);

            Menu::create([
                'hotel_id' => $hotel_id,
                'type' => $request->type,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu added succesfully');
    }

    public function menu_data($menu_id)
    {
        try {
            $menu = Menu::where('id', intval($menu_id))->first();

            $menu_id = $menu->id;
            $menu_name = $menu->name;
            $menu_description = $menu->description;
            $menu_price = $menu->price;
            $menu_image = $menu->image;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'menu_id' => $menu_id,
            'menu_name' => $menu_name,
            'menu_description' => $menu_description,
            'menu_price' => $menu_price,
            'menu_image' => $menu_image,
        ]);
    }

    public function menu_update(Request $request, $menu_id)
    {
        $validator = Validator::make($request->all(), [
            // 'type' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $menu = Menu::where('id', intval($menu_id))->first();

        try {
            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu updated succesfully');
    }

    public function update_menu_image(Request $request, $menu_id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $menu = Menu::where('id', intval($menu_id))->first();

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/menu/" . $file_name);
            $request->image->move(public_path('uploads/menu/'), $file_name);

            $menu->update([
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update menu image',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu image updated succesfully');
    }

    public function menu_delete(Request $request, $menu_id)
    {
        $menu = Menu::find($menu_id)->first();
        $deleted = $menu->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed to delete menu"
            ], 400);
        }

        return response()->json([
            "message" => "menu deleted successfully"
        ]);
    }
}
