<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function room_list($hotel_id)
    {
        $rooms = Television::where('hotel_id', $hotel_id)->get();

        $room_list = [];
        foreach ($rooms as $room) {
            $room_id = $room->id;
            $room_number = $room->room_number;

            $room_list[] = [
                'room_id' => $room_id,
                'room_number' => $room_number
            ];
        }

        return response()->json($room_list);
    }

    public function guest(Request $request, $room_number_id)
    {
        $room = Television::where('id', $room_number_id)->first();

        // dd($room);

        $validator = Validator::make($request->all(), [
            'room_type' => 'required',
            'guest_name' => 'required',
            'guest_gender' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
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
