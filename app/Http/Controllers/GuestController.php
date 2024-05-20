<?php

namespace App\Http\Controllers;

use App\Models\GuestHistory;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function room_number_list($hotel_id)
    {
        $televisions = Television::where('hotel_id', $hotel_id)
            ->where('guest_name', null)
            ->get();

        $room_numbers = [];
        foreach ($televisions as $television) {
            $room_id = $television->id;
            $room_number = $television->room_number;

            $room_numbers[] = [
                'room_number_id' => $room_id,
                'room_number' => $room_number
            ];
        }

        return response()->json([
            'room_numbers' => $room_numbers,
        ]);
    }

    public function room_type_list($hotel_id)
    {
        $rooms = Room::where('hotel_id', $hotel_id)->get();

        $room_types = [];
        foreach ($rooms as $room) {
            $room_type_id = $room->id;
            $room_type = $room->type;

            $room_types[] = [
                'room_type_id' => $room_type_id,
                'room_type' => $room_type
            ];
        }

        return response()->json([
            'room_type' => $room_types,
        ]);
    }

    public function add_guest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number_id' => 'required',
            'room_type' => 'required',
            'guest_name' => 'required',
            'guest_gender' => 'required',
            'check_in' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $room = Television::where('id', $request->room_number_id)->first();

            $room->update([
                'room_type' => $request->room_type,
                'guest_name' => $request->guest_name,
                'guest_gender' => $request->guest_gender,
                'check_in' => $request->check_in,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save guest data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('guest data added succesfully');
    }

    public function guest_list($hotel_id)
    {
        $televisions = Television::where('hotel_id', $hotel_id)
            ->where('guest_name', '!=', null)
            ->get();

        try {
            $guest_list = [];
            foreach ($televisions as $television) {
                $room_number_id = $television->id;
                $room_number = $television->room_number;
                $room_type = $television->room_type;
                $guest_name = $television->guest_name;
                $check_in = $television->check_in;

                $guest_list[] = [
                    'room_number_id' => $room_number_id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'guest_name' => $guest_name,
                    'check_in' => $check_in,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get guest list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'guest_list' => $guest_list
        ]);
    }

    public function guest_data($room_number_id)
    {
        $television = Television::where('id', $room_number_id)->first();

        try {
            $room_number = $television->room_number;
            $room_type = $television->room_type;
            $guest_name = $television->guest_name;
            $guest_gender = $television->guest_gender;
            $check_in = $television->check_in;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get guest data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_number' => $room_number,
            'room_type' => $room_type,
            'guest_name' => $guest_name,
            'guest_gender' => $guest_gender,
            'check_in' => $check_in,
        ]);
    }

    public function update_guest(Request $request, $room_number_id)
    {
        $validator = Validator::make($request->all(), [
            'room_type' => 'required',
            'guest_name' => 'required',
            'guest_gender' => 'required',
            'check_in' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $television = Television::where('id', $room_number_id)->first();

        try {
            $television->update([
                'room_type' => $request->room_type,
                'guest_name' => $request->guest_name,
                'guest_gender' => $request->guest_gender,
                'check_in' => $request->check_in,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update guest data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('guest data updated succesfully');
    }

    public function check_out(Request $request, $room_number_id)
    {
        $validator = Validator::make($request->all(), [
            'check_out' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('id', $room_number_id)->first();

            DB::beginTransaction();

            GuestHistory::create([
                'hotel_id' => $television->hotel_id,
                'room_number' => $television->room_number,
                'guest_name' => $television->guest_name,
                'check_in' => $television->check_in,
                'check_out' => $request->check_out,
            ]);

            $television->update([
                'guest_name' => null,
                'guest_gender' => null,
                'room_type' => null,
                'check_in' => null,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to save check out time',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'success add data to history '
        ]);
    }

    public function delete_guest($room_number_id)
    {
        $television = Television::where('id', $room_number_id)->first();

        try {
            $television->update([
                'room_type' => null,
                'guest_name' => null,
                'guest_gender' => null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to delete guest data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            "message" => "guest data deleted successfully"
        ]);
    }
}
