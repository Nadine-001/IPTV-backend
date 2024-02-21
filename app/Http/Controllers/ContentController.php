<?php

namespace App\Http\Controllers;

// use App\Models\Content;
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
    public function greeting(Request $request, $hotel_id)
    {
        $hotel = Hotel::where('id', intval($hotel_id))->first();

        $validator = Validator::make($request->all(), [
            'greeting' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

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

    public function hotel_name($hotel_id)
    {
        $hotel = Hotel::where('id', intval($hotel_id))->first();

        try {
            $hotel_name = $hotel->name;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel name',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_name' => $hotel_name,
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
            'hotel_photo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->hotel_photo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_hotel_photo = asset("uploads/hotel_about/" . $file_name);
            $request->hotel_photo->move(public_path('uploads/hotel_about/'), $file_name);

            $hotel->update([
                // 'name' => $request->hotel_name,
                'class' => $request->hotel_class,
                'about' => $request->hotel_about,
                'check_in' => $request->hotel_check_in,
                'check_out' => $request->hotel_check_out,
                'photo' => $path_hotel_photo,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel info',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel info added succesfully');
    }

    public function hotel_facilities_create(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/hotel_facilities/" . $file_name);
            $request->image->move(public_path('uploads/hotel_facilities/'), $file_name);

            HotelFacilities::create([
                'hotel_id' => $hotel_id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $path_image
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save hotel facilities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel facilities added succesfully');
    }

    public function hotel_facilities_update(Request $request, $facility_id)
    {
        $facility = HotelFacilities::where('id', $facility_id)->first();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $file_name = time() . " - " . $request->image->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_image = asset("uploads/hotel_facilities/" . $file_name);
            $request->image->move(public_path('uploads/hotel_facilities/'), $file_name);

            $facility->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $path_image
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update hotel facilities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel facilities updated succesfully');
    }

    public function hotel_facilities_delete($facility_id)
    {
        $facility = HotelFacilities::where('id', intval($facility_id))->first();
        $deleted = $facility->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed delete hotel facility"
            ], 400);
        }

        return response()->json([
            "message" => "hotel facility deleted successfully"
        ]);
    }

    public function room_about_create(Request $request, $hotel_id)
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

    public function room_about_update(Request $request, $room_id)
    {
        $room = Room::where('id', $room_id)->first();

        $validator = Validator::make($request->all(), [
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

            $room->update([
                'facility' => $request->facility,
                'description' => $request->description,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update room about',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('room about updated succesfully');
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

    public function amenities_update(Request $request, $service_id)
    {
        $service = RoomService::where('id', intval($service_id))->first();

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

            $service->update([
                'name' => $request->name,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update amenities',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('amenities updated succesfully');
    }

    public function amenities_delete(Request $request, $service_id)
    {
        $service = RoomService::where('id', intval($service_id))->first();
        $deleted = $service->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed delete room service"
            ], 400);
        }

        return response()->json([
            "message" => "room service deleted successfully"
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

    public function menu_update(Request $request, $menu_id)
    {
        $menu = Menu::where('id', intval($menu_id))->first();

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

            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $path_image,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to updated menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('menu updated succesfully');
    }

    public function menu_delete(Request $request, $menu_id)
    {
        $menu = Menu::find($menu_id)->first();
        $deleted = $menu->delete();

        if (!$deleted) {
            return response()->json([
                "message" => "failed delete menu"
            ], 400);
        }

        return response()->json([
            "message" => "menu deleted successfully"
        ]);
    }

    // public function add_content(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'greeting' => 'required',
    //         'hotel_name' => 'required',
    //         'hotel_class' => 'required',
    //         // 'hotel_logo' => 'required',
    //         'hotel_about' => 'required',
    //         'hotel_check_in' => 'required',
    //         'hotel_check_out' => 'required',
    //         'hotel_photo' => 'required',
    //         'hotel_address' => 'required',
    //         'hotel_city' => 'required',
    //         'hotel_phone' => 'required',
    //         'facility_name' => 'required',
    //         'facility_image' => 'required',
    //         'facility_description' => 'required',
    //         'room_type' => 'required',
    //         'room_facility' => 'required',
    //         'room_description' => 'required',
    //         'room_image' => 'required',
    //         'room_television' => 'required',
    //         'room_service_name' => 'required',
    //         'room_service_image' => 'required',
    //         'menu_name' => 'required',
    //         'menu_description' => 'required',
    //         'menu_price' => 'required',
    //         'menu_rating' => 'required',
    //         'menu_image' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors());
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $file_name = time() . " - " . $request->hotel_photo->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_hotel_photo = asset("uploads/registrations/" . $file_name);
    //         $request->hotel_photo->move(public_path('uploads/registrations/'), $file_name);

    //         $file_name = time() . " - " . $request->hotel_logo->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_hotel_logo = asset("uploads/registrations/" . $file_name);
    //         $request->hotel_logo->move(public_path('uploads/registrations/'), $file_name);

    //         $hotel = Hotel::create([
    //             'name' => $request->hotel_name,
    //             'class' => $request->hotel_class,
    //             'check_in' => $request->hotel_check_in,
    //             'check_out' => $request->hotel_check_out,
    //             'greeting' => $request->greeting,
    //             'about' => $request->hotel_about,
    //             'photo' => $path_hotel_photo,
    //             'address' => $request->hotel_address,
    //             'city' => $request->hotel_city,
    //             'phone' => $request->hotel_phone,
    //             'logo' => $path_hotel_logo,
    //             'order_food_intro' => $request->order_food_intro,
    //             'qr_code_payment' => $request->qr_code_payment,
    //             'qr_code_wifi' => $request->qr_code_wifi,
    //         ]);

    //         $file_name = time() . " - " . $request->facility_image->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_facility_image = asset("uploads/registrations/" . $file_name);
    //         $request->facility_image->move(public_path('uploads/registrations/'), $file_name);

    //         $hotel_facility = HotelFacilities::create([
    //             'hotel_id' => $hotel->id,
    //             'name' => $request->facility_name,
    //             'description' => $request->facility_description,
    //             'image' => $path_facility_image
    //         ]);

    //         $file_name = time() . " - " . $request->room_image->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_room_image = asset("uploads/registrations/" . $file_name);
    //         $request->room_image->move(public_path('uploads/registrations/'), $file_name);

    //         $room = Room::create([
    //             'hotel_id' => $hotel->id,
    //             'type' => $request->room_type,
    //             'facility' => $request->room_facility,
    //             'description' => $request->room_description,
    //             'image' => $path_room_image,
    //             'television' => $request->room_television,
    //         ]);

    //         $file_name = time() . " - " . $request->room_service_image->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_room_service_image = asset("uploads/registrations/" . $file_name);
    //         $request->room_service_image->move(public_path('uploads/registrations/'), $file_name);

    //         $room_service = RoomService::create([
    //             'room_id' => $room->id,
    //             'name' => $request->room_service_name,
    //             'image' => $path_room_service_image,
    //         ]);

    //         $file_name = time() . " - " . $request->menu_image->getClientOriginalName();
    //         $file_name = str_replace(' ', '', $file_name);
    //         $path_menu_image = asset("uploads/registrations/" . $file_name);
    //         $request->menu_image->move(public_path('uploads/registrations/'), $file_name);

    //         $menu = Menu::create([
    //             'hotel_id' => $hotel->id,
    //             'name' => $request->menu_name,
    //             'description' => $request->menu_description,
    //             'price' => $request->menu_price,
    //             'rating' => $request->menu_rating,
    //             'image' => $path_menu_image,
    //         ]);

    //         DB::commit();
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'failed to save data',
    //             'errors' => $th->getMessage()
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Data saved successfully!',
    //         'hotel' => $hotel,
    //         'hotel_facility' => $hotel_facility,
    //         'room' => $room,
    //         'room_service' => $room_service,
    //         'menu' => $menu,
    //     ]);
    // }

    // public function show_content()
    // {
    //     return response()->json(Content::all());
    // }
}
