<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function room_number_list($hotel_id)
    {
        $televisions = Television::where('hotel_id', $hotel_id)->get();

        $room_number = [];
        foreach ($televisions as $television) {
            $room_id = $television->id;
            $room_number = $television->room_number;

            $room_number[] = [
                'room_id' => $room_id,
                'room_number' => $room_number
            ];
        }

        return response()->json([
            'room_number' => $room_number,
        ]);
    }

    public function room_type_list($hotel_id)
    {
        $rooms = Room::where('hotel_id', $hotel_id)->get();

        $room_type = [];
        foreach ($rooms as $room) {
            $room_type_id = $room->id;
            $room_type = $room->type;

            $room_type[] = [
                'room_type_id' => $room_type_id,
                'room_type' => $room_type
            ];
        }

        return response()->json([
            'room_type' => $room_type,
        ]);
    }

    public function guest(Request $request, $room_number_id)
    {
        $room = Television::where('id', $room_number_id)->first();

        $validator = Validator::make($request->all(), [
            'room_type' => 'required',
            'guest_name' => 'required',
            'guest_gender' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $room->update([
                'room_type' => $request->room_type,
                'guest_name' => $request->guest_name,
                'guest_gender' => $request->guest_gender,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save guest',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('guest added succesfully');
    }
}
