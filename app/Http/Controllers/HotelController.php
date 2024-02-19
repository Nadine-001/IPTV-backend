<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelFacilities;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function hotel_greeting(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'television_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();
            $television = Television::where('id', $request->television_id)->first();

            $room_number = $television->room_number;
            $room_type = $television->room_type;
            $guest_name = $television->guest_name;
            $guest_gender = $television->guest_gender;
            $hotel_greeting = $hotel->greeting;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel greeting',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_number' => $room_number,
            'room_type' => $room_type,
            'guest_name' => $guest_name,
            'guest_gender' => $guest_gender,
            'hotel_greeting' => $hotel_greeting,
        ]);
    }

    public function home(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'television_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $hotel = Hotel::where('id', $hotel_id)->first();
            $television = Television::where('id', $request->television_id)->first();

            $room_number = $television->room_number;
            $room_type = $television->room_type;
            $guest_name = $television->guest_name;
            $hotel_wifi = $hotel->qr_code_wifi;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get home content',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_number' => $room_number,
            'room_type' => $room_type,
            'guest_name' => $guest_name,
            'hotel_wifi' => $hotel_wifi,
        ]);
    }

    public function hotel_about($hotel_id)
    {
        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $hotel_name = $hotel->name;
            $hotel_class = $hotel->class;
            $hotel_about = $hotel->about;
            $hotel_check_in = $hotel->check_in;
            $hotel_check_out = $hotel->check_out;
            $hotel_photo = $hotel->photo;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel about',
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
        ]);
    }

    public function hotel_location($hotel_id)
    {
        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

            $hotel_address = $hotel->address;
            $hotel_longitude = $hotel->longitude;
            $hotel_langitude = $hotel->langitude;
            $hotel_phone = $hotel->phone;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel location',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_address' => $hotel_address,
            'hotel_longitude' => $hotel_longitude,
            'hotel_langitude' => $hotel_langitude,
            'hotel_phone' => $hotel_phone,
        ]);
    }

    public function hotel_facilites($hotel_id)
    {
        try {
            // $hotel = Hotel::findOrFail($hotel_id);
            $facilities = HotelFacilities::where('hotel_id', $hotel_id)->get();

            $facilities_data = [];

            foreach ($facilities as $facility) {
                $facility_name = $facility->name;
                $facility_description = $facility->description;
                $facility_image = $facility->image;

                $facilities_data[] = [
                    'facility_name' => $facility_name,
                    'facility_description' => $facility_description,
                    'facility_image' => $facility_image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel facilites',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($facilities_data);
    }
}
