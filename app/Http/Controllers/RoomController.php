<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomService;
use App\Models\Television;

class RoomController extends Controller
{
    public function room_header($room_number_id)
    {
        try {
            $television = Television::where('id', $room_number_id)->first();

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

    public function room_about($room_number_id)
    {
        try {
            $television = Television::where('id', $room_number_id)->first();

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

    public function room_service($hotel_id)
    {
        try {
            $services = RoomService::where('hotel_id', $hotel_id)->get();

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
