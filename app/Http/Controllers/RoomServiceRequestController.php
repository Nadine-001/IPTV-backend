<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomService;
use App\Models\RoomServiceRequest;
use App\Models\RoomServiceRequestDetail;
use App\Models\Television;
use App\Models\TempCartRoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomServiceRequestController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'service_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            TempCartRoomService::create([
                'hotel_id' => $hotel->id,
                'television_id' => $television->id,
                'room_service_id' => $request->service_id,
                'qty' => 1,
                'note' => $request->note,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add to cart',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address' => $request->mac_address,
            'service_id' => $request->service_id,
            'note' => $request->note,
        ]);
    }

    public function show_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $carts = TempCartRoomService::where('television_id', $television->id)->get();

            $request_list = [];
            foreach ($carts as $cart) {
                $room_service = RoomService::where('id', $cart->room_service_id)->first();

                $item_id = $cart->id;
                $room_service_id = $room_service->id;
                $room_service_name = $room_service->name;
                $room_service_image = $room_service->image;
                $quantity = $cart->qty;
                $note = $cart->note;

                $request_list[] = [
                    'item_id' => $item_id,
                    'room_service_id' => $room_service_id,
                    'room_service_name' => $room_service_name,
                    'room_service_image' => $room_service_image,
                    'quantity' => $quantity,
                    'note' => $note,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to place order',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($request_list);
    }

    public function increase_item(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $item = TempCartRoomService::where('id', $item_id)->first();
            $item->update([
                'qty' => $item->qty + 1
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to increase item',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('item increased succesfully');
    }

    public function decrease_item(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $item = TempCartRoomService::where('id', $item_id)->first();
            $item->update([
                'qty' => $item->qty - 1
            ]);

            if ($item->qty < 1) {
                $deleted = $item->delete();

                if (!$deleted) {
                    return response()->json([
                        "message" => "failed to delete item"
                    ], 500);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to decrease item',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('item decreased succesfully');
    }

    public function delete_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $item = TempCartRoomService::where('id', $request->item_id)->first();
            $deleted = $item->delete();

            if (!$deleted) {
                return response()->json([
                    "message" => "failed to delete item"
                ], 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to delete item',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('item deleted succesfully');
    }

    public function request_service(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'requests' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            $room_service_request = RoomServiceRequest::create([
                'hotel_id' => $hotel->id,
                'television_id' => $television->id,
            ]);

            foreach ($request->requests as $req) {
                RoomServiceRequestDetail::create([
                    'room_service_request_id' => $room_service_request->id,
                    'room_service_id' => $req['room_service_id'],
                    'qty' => $req['quantity'],
                ]);

                $item = TempCartRoomService::where('id', $req['item_id'])->first();
                $deleted = $item->delete();

                if (!$deleted) {
                    return response()->json([
                        "message" => "failed to delete item"
                    ], 500);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to send requests',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address' => $request->mac_address,
            'requests' => $request->requests,
        ]);
    }
}
