<?php

namespace App\Http\Controllers;

use App\Models\FoodServiceRequest;
use App\Models\FoodServiceRequestDetail;
use App\Models\Menu;
use App\Models\RoomService;
use App\Models\RoomServiceRequest;
use App\Models\RoomServiceRequestDetail;
use App\Models\Television;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function room_service_list($hotel_id)
    {
        $room_service_request = RoomServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', null)
            ->get();

        try {
            $service_request_list = [];

            foreach ($room_service_request as $service_request) {
                $television_id = $service_request->television_id;
                $television = Television::where('id', $television_id)->first(); // first?? get??

                $room_number = $television->room_number;
                $room_type = $television->room_type;

                $service_request_list = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($service_request_list);
    }

    public function room_service_detail($room_service_request_id)
    {
        $room_service_request = RoomServiceRequest::where('id', $room_service_request_id)->first();
        $request_services = RoomServiceRequestDetail::where('room_service_request_id', $room_service_request->id)->get();

        try {
            $request_list = [];

            foreach ($request_services as $request_service) {
                $room_service_id = $request_service->room_service_id;
                $room_service = RoomService::where('id', $room_service_id)->first();

                $service_name = $room_service->name;
                $service_image = $room_service->image;
                $request_note = $request_service->note;
                $request_qty = $request_service->qty;

                $request_list[] = [
                    'service_name' => $service_name,
                    'service_image' => $service_image,
                    'request_note' => $request_note,
                    'request_qty' => $request_qty,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request detail',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($request_list);
    }

    public function accept_service_request($room_service_request_id)
    {
        $room_service_request = RoomServiceRequest::where('id', $room_service_request_id)->first();

        try {
            $is_accepted = 1;
            $room_service_request->update([
                'is_accepted' => $is_accepted
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to accept the request',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'is_accepted' => $is_accepted
        ]);
    }

    public function decline_service_request($room_service_request_id)
    {
        $room_service_request = RoomServiceRequest::where('id', $room_service_request_id)->first();

        try {
            $is_accepted = 0;
            $room_service_request->update([
                'is_accepted' => $is_accepted
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to decline the request',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'is_accepted' => $is_accepted
        ]);
    }

    public function room_service_history($hotel_id)
    {
        $room_service_request = RoomServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', '!=', null)
            ->get();

        try {
            $service_history_list = [];

            foreach ($room_service_request as $service_request) {
                $is_accepted = $service_request->is_accepted;

                $television_id = $service_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;
                $room_type = $television->room_type;

                $request_services = RoomServiceRequestDetail::where('room_service_request_id', $service_request->id)->get();

                $request_detail = [];

                foreach ($request_services as $request_service) {
                    $room_service_id = $request_service->room_service_id;
                    $room_service = RoomService::where('id', $room_service_id)->first();

                    $service_name = $room_service->name;
                    $service_image = $room_service->image;

                    $quantity = $request_service->qty;
                    $note = $request_service->note;

                    $request_detail[] = [
                        'menu_id' => $room_service_id,
                        'service_name' => $service_name,
                        'service_image' => $service_image,
                        'quantity' => $quantity,
                        'note' => $note,
                    ];
                }

                $service_history_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'service_detail' => $request_detail,
                    'is_accepted' => $is_accepted,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request history',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'service_history_list' => $service_history_list
        ]);
    }

    public function food_service_list($hotel_id)
    {
        $food_service_request = FoodServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', null)
            ->get();

        try {
            $food_request_list = [];

            foreach ($food_service_request as $service_request) {
                $television_id = $service_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;
                $room_type = $television->room_type;

                $food_request_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get food service request list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($food_request_list);
    }

    public function food_service_detail($food_service_request_id)
    {
        $food_service_request = FoodServiceRequest::where('id', $food_service_request_id)->first();
        $food_services = FoodServiceRequestDetail::where('food_service_request_id', $food_service_request->id)->get();

        try {
            $order_list = [];

            foreach ($food_services as $food_service) {
                $menu_id = $food_service->menu_id;
                $menu = Menu::where('id', $menu_id)->first();

                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_image = $menu->image;
                $request_qty = $food_service->qty;
                $request_total = $food_service_request->total;

                $order_list[] = [
                    'menu_name' => $menu_name,
                    'menu_image' => $menu_image,
                    'menu_description' => $menu_description,
                    'menu_price' => $menu_price,
                    'request_qty' => $request_qty,
                    'request_total' => $request_total,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get food service request detail',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($order_list);
    }

    public function accept_food_order($food_service_request_id)
    {
        $food_order = FoodServiceRequest::where('id', $food_service_request_id)->first();

        try {
            $is_accepted = 1;
            $food_order->update([
                'is_accepted' => $is_accepted
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to accept the order',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'is_accepted' => $is_accepted
        ]);
    }

    public function decline_food_order($food_service_request_id)
    {
        $food_order = FoodServiceRequest::where('id', $food_service_request_id)->first();

        try {
            $is_accepted = 0;
            $food_order->update([
                'is_accepted' => $is_accepted
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to decline the order',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'is_accepted' => $is_accepted
        ]);
    }

    public function food_service_history($hotel_id)
    {
        $food_order_request = FoodServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', '!=', null)
            ->get();

        try {
            $order_history_list = [];

            foreach ($food_order_request as $order_request) {
                $is_accepted = $order_request->is_accepted;

                $television_id = $order_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;
                $room_type = $television->room_type;

                $food_orders = FoodServiceRequestDetail::where('food_service_request_id', $order_request->id)->get();

                $order_detail = [];

                foreach ($food_orders as $food_order) {
                    $menu_id = $food_order->menu_id;
                    $menu = Menu::where('id', $menu_id)->first();

                    $menu_name = $menu->name;
                    $menu_price = $menu->price;
                    $menu_image = $menu->image;
                    $quantity = $food_order->qty;
                    $total = $food_order->total;

                    $order_detail[] = [
                        'menu_id' => $menu_id,
                        'menu_name' => $menu_name,
                        'menu_price' => $menu_price,
                        'menu_image' => $menu_image,
                        'quantity' => $quantity,
                        'total' => $total,
                    ];
                }

                $order_history_list[] = [
                    'id' => $order_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'order_detail' => $order_detail,
                    'is_accepted' => $is_accepted,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request history',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'order_history_list' => $order_history_list
        ]);
    }
}
