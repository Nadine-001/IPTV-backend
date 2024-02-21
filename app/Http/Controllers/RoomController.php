<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomService;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function room_header(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();

            $guest_name = $television->guest_name;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get header',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'guest_name' => $guest_name,
        ]);
    }

    public function room_about(Request $request)
    {
        try {
            $television = Television::where('mac_address', $request->mac_address)->first();

            $room_number = $television->room_number;
            $room_type = $television->room_type;

            $room = Room::where('type', $room_type)->first();

            $room_facility = $room->facility;
            $room_description = $room->description;
            $room_image = $room->image;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room about',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_number' => $room_number,
            'room_type' => $room_type,
            'room_facility' => $room_facility,
            'room_facility' => $room_description,
            'room_image' => $room_image,
        ]);
    }

    public function room_service(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();
            $services = RoomService::where('hotel_id', $hotel->id)->get();

            $services_data = [];

            foreach ($services as $service) {
                $service_name = $service->name;
                $service_image = $service->image;

                $services_data[] = [
                    'service_name' => $service_name,
                    'service_image' => $service_image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'services_data' => $services_data
        ]);
    }
}
