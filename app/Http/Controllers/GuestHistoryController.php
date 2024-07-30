<?php

namespace App\Http\Controllers;

use App\Models\GuestHistory;
use App\Models\Room;
use App\Models\Television;
use Illuminate\Http\Request;

class GuestHistoryController extends Controller
{
    public function statistic($hotel_id)
    {
        try {
            $past_guest = GuestHistory::where('hotel_id', $hotel_id)->count();

            $guest_histories = GuestHistory::where('hotel_id', $hotel_id)->get();

            $room_type_statistic = [];
            foreach ($guest_histories as $guest) {
                $room_type = $guest->room_type;
                if (!isset($room_type_statistic[$room_type])) {
                    $room_type_statistic[$room_type] = 0;
                }

                $room_type_statistic[$room_type]++;
            }

            $guest = Television::where('hotel_id', $hotel_id)->where('check_in', '!=', NULL)->count();
            $available_room = Television::where('hotel_id', $hotel_id)->where('check_in', NULL)->count();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get guest statistic',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'guest_total' => $past_guest + $guest,
            'guest_check_in' => $guest,
            'available_room' => $available_room,
            'room_type_statistic' => $room_type_statistic
        ]);
    }

    public function guest_history($hotel_id)
    {
        $guest_histories = GuestHistory::where('hotel_id', $hotel_id)->get();

        try {
            $guest_history = [];

            foreach ($guest_histories as $guest_history) {
                $check_in = $guest_history->check_in;
                $check_out = $guest_history->checkout;
                $room_number = $guest_history->room_number;
                $room_type = $guest_history->room_type;
                $guest_name = $guest_history->guest_name;

                $guest_history = [
                    'check_in' => $check_in,
                    'check_out' => $check_out,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'guest_name' => $guest_name,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get guest history',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json(['guest_history' => $guest_history]);
    }
}
