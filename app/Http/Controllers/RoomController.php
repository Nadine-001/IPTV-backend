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
    public function header($hotel_id, $room_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id)->first();
            $television = Television::where('room_id', $room_id)->first();

            $guest_name = $television->guest_name;
            $hotel_logo = $hotel->logo;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get header',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'guest_name' => $guest_name,
            'hotel_logo' => $hotel_logo,
        ]);
    }

    public function room_about(Request $request, $hotel_id, $room_id)
    {
        try {
            $room = Room::findOrFail($room_id)->first();

            $room_number = $room->number;
            $room_type = $room->type;
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

    public function room_service(Request $request, $room_id)
    {
        try {
            $services = RoomService::where('room_id', $room_id)->get();

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

        return response()->json($services_data);
    }
}
