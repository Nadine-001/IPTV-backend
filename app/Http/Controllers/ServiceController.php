<?php

namespace App\Http\Controllers;

use App\Models\FoodServiceRequest;
use App\Models\FoodServiceRequestDetail;
use App\Models\Menu;
use App\Models\RoomService;
use App\Models\RoomServiceRequest;
use App\Models\RoomServiceRequestDetail;
use App\Models\Television;
use ElephantIO\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;
                $room_type = $television->room_type;

                $room_service_request = RoomServiceRequest::where('id', $service_request->id)->first();
                $request_services = RoomServiceRequestDetail::where('room_service_request_id', $room_service_request->id)->get();

                $request_detail = [];
                foreach ($request_services as $request_service) {
                    $room_service_id = $request_service->room_service_id;
                    $room_service = RoomService::where('id', $room_service_id)->first();

                    $service_name = $room_service->name;
                    $service_image = $room_service->image;
                    $request_qty = $request_service->qty;
                    $created_at = $room_service_request->created_at;

                    $request_detail[] = [
                        'service_id' => $room_service_id,
                        'service_name' => $service_name,
                        'service_image' => $service_image,
                        'request_qty' => $request_qty,
                        'created_at' => $created_at->format('Y-m-d H:i'),
                    ];
                }

                $service_request_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'request_detail' => $request_detail,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'service_request_list' => $service_request_list
        ]);
    }

    // public function room_service_detail($room_service_request_id)
    // {
    //     $room_service_request = RoomServiceRequest::where('id', $room_service_request_id)->first();
    //     $request_services = RoomServiceRequestDetail::where('room_service_request_id', $room_service_request->id)->get();

    //     try {
    //         $request_list = [];

    //         foreach ($request_services as $request_service) {
    //             $room_service_id = $request_service->room_service_id;
    //             $room_service = RoomService::where('id', $room_service_id)->first();

    //             $service_name = $room_service->name;
    //             $service_image = $room_service->image;
    //             $request_qty = $request_service->qty;

    //             $request_list[] = [
    //                 'service_id' => $room_service_id,
    //                 'service_name' => $service_name,
    //                 'service_image' => $service_image,
    //                 'request_qty' => $request_qty,
    //             ];
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'failed to get room service request detail',
    //             'errors' => $th->getMessage()
    //         ], 400);
    //     }

    //     return response()->json($request_list);
    // }

    public function accept_service_request($room_service_request_id)
    {
        $room_service_request = RoomServiceRequest::where('id', $room_service_request_id)->first();

        try {
            $is_accepted = 1;
            $room_service_request->update([
                'is_accepted' => $is_accepted
            ]);

            $url = 'https://iptv-hms.socket.dev.mas-ts.com';
            // $url = 'http://10.218.15.221:8000';

            $options = ['client' => Client::CLIENT_4X];

            $client = Client::create($url, $options);
            $client->connect();

            $data = [
                'mac_address' => $room_service_request->television->mac_address,
                'message' => "Your request has been delivered!"
            ];

            $client->emit('isDelivered', $data);

            $client->disconnect();
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
                $updated_at = $service_request->updated_at;

                $television_id = $service_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;

                $request_services = RoomServiceRequestDetail::where('room_service_request_id', $service_request->id)->get();

                $request_detail = [];

                foreach ($request_services as $request_service) {
                    $room_service_id = $request_service->room_service_id;
                    $room_service = RoomService::where('id', $room_service_id)->first();

                    $service_name = $room_service->name;
                    $service_image = $room_service->image;

                    $quantity = $request_service->qty;

                    $request_detail[] = [
                        'room_service_id' => $room_service_id,
                        'service_name' => $service_name,
                        'service_image' => $service_image,
                        'quantity' => $quantity,
                    ];
                }

                $service_history_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'service_detail' => $request_detail,
                    'is_accepted' => $is_accepted,
                    'done_at' => $updated_at->format('Y-m-d H:i'),
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

                $food_service_request = FoodServiceRequest::where('id', $service_request->id)->first();
                $food_services = FoodServiceRequestDetail::where('food_service_request_id', $food_service_request->id)->get();

                $order_detail = [];
                foreach ($food_services as $food_service) {
                    $menu_id = $food_service->menu_id;
                    $menu = Menu::where('id', $menu_id)->first();

                    $menu_name = $menu->name;
                    $menu_description = $menu->description;
                    $menu_price = $menu->price;
                    $menu_image = $menu->image;
                    $order_qty = $food_service->qty;
                    $order_total = $food_service_request->total;
                    $order_payment = $food_service_request->payment_method;
                    $order_status = $food_service_request->is_paid;
                    $created_at = $food_service_request->created_at;

                    $order_detail[] = [
                        'menu_id' => $menu_id,
                        'menu_name' => $menu_name,
                        'menu_image' => $menu_image,
                        'menu_description' => $menu_description,
                        'menu_price' => $menu_price,
                        'order_qty' => $order_qty,
                    ];
                }

                $food_request_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'order_detail' => $order_detail,
                    'order_total' => $order_total,
                    'order_payment' => $order_payment,
                    'order_status' => $order_status,
                    'created_at' => $created_at->format('Y-m-d H:i'),
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get food service request list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'food_request_list' => $food_request_list
        ]);
    }

    // public function food_service_detail($food_service_request_id)
    // {
    //     $food_service_request = FoodServiceRequest::where('id', $food_service_request_id)->first();
    //     $food_services = FoodServiceRequestDetail::where('food_service_request_id', $food_service_request->id)->get();

    //     try {
    //         $order_list = [];

    //         foreach ($food_services as $food_service) {
    //             $menu_id = $food_service->menu_id;
    //             $menu = Menu::where('id', $menu_id)->first();

    //             $menu_name = $menu->name;
    //             $menu_description = $menu->description;
    //             $menu_price = $menu->price;
    //             $menu_image = $menu->image;
    //             $request_qty = $food_service->qty;
    //             $request_total = $food_service_request->total;

    //             $order_list[] = [
    //                 'menu_id' => $menu_id,
    //                 'menu_name' => $menu_name,
    //                 'menu_image' => $menu_image,
    //                 'menu_description' => $menu_description,
    //                 'menu_price' => $menu_price,
    //                 'request_qty' => $request_qty,
    //                 'request_total' => $request_total,
    //             ];
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'failed to get food service request detail',
    //             'errors' => $th->getMessage()
    //         ], 400);
    //     }

    //     return response()->json($order_list);
    // }

    public function accept_food_order($food_service_request_id)
    {
        $food_order = FoodServiceRequest::where('id', $food_service_request_id)->first();

        try {
            $is_accepted = 1;
            $food_order->update([
                'is_accepted' => $is_accepted
            ]);

            $url = 'https://iptv-hms.socket.dev.mas-ts.com';
            // $url = 'http://10.218.15.221:8000';

            $options = ['client' => Client::CLIENT_4X];

            $client = Client::create($url, $options);
            $client->connect();

            $data = [
                'mac_address' => $food_order->television->mac_address,
                'message' => "Your order has been delivered!"
            ];

            $client->emit('isDelivered', $data);

            $client->disconnect();
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

    public function payment_status($food_service_request_id)
    {
        try {
            $food_order = FoodServiceRequest::where('id', $food_service_request_id)->first();

            $status = $food_order->is_paid;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get payment status',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'status' => $status
        ]);
    }

    public function change_status(Request $request, $food_service_request_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_order = FoodServiceRequest::where('id', $food_service_request_id)->first();

            $food_order->update([
                'is_paid' => $request->status
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get payment status',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'status' => $request->status
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
                $total = $order_request->total;
                $updated_at = $order_request->updated_at;

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

                    $order_detail[] = [
                        'menu_id' => $menu_id,
                        'menu_name' => $menu_name,
                        'menu_price' => $menu_price,
                        'menu_image' => $menu_image,
                        'quantity' => $quantity,
                    ];
                }

                $order_history_list[] = [
                    'id' => $order_request->id,
                    'room_number' => $room_number,
                    'room_type' => $room_type,
                    'order_detail' => $order_detail,
                    'is_accepted' => $is_accepted,
                    'total' => $total,
                    'done_at' => $updated_at->format('Y-m-d H:i'),
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

    public function room_service_statistic($hotel_id)
    {
        $room_services = RoomService::where('hotel_id', $hotel_id)->get();

        try {
            $statistic = [];

            foreach ($room_services as $room_service) {
                $count = RoomServiceRequestDetail::where('room_service_id', $room_service->id)->count();

                $statistic[$room_service->name] = $count;
            }

            $undelivered_total = RoomServiceRequest::where('hotel_id', $hotel_id)->where('is_accepted', 0)->count();
            $request_used = RoomServiceRequest::where('hotel_id', $hotel_id)->count();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service statistic',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'statistic' => $statistic,
            'undelivered_total' => $undelivered_total,
            'request_used' => $request_used,
        ]);
    }

    public function room_service_table($hotel_id)
    {
        $room_service_request = RoomServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', '!=', null)
            ->get();

        try {
            $room_service_list = [];

            foreach ($room_service_request as $service_request) {
                $created_at = $service_request->created_at;

                $television_id = $service_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;

                $request_services = RoomServiceRequestDetail::where('room_service_request_id', $service_request->id)->get();

                $request_detail = [];

                foreach ($request_services as $request_service) {
                    $room_service_id = $request_service->room_service_id;
                    $room_service = RoomService::where('id', $room_service_id)->first();

                    $service_name = $room_service->name;
                    $service_image = $room_service->image;

                    $quantity = $request_service->qty;

                    $request_detail[] = [
                        'room_service_id' => $room_service_id,
                        'service_name' => $service_name,
                        'service_image' => $service_image,
                        'quantity' => $quantity,
                    ];
                }

                $room_service_list[] = [
                    'id' => $service_request->id,
                    'room_number' => $room_number,
                    'service_detail' => $request_detail,
                    'created_at' => $created_at->format('Y-m-d H:i'),
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service table',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_service_list' => $room_service_list
        ]);
    }

    public function food_order_statistic($hotel_id)
    {
        $menus = FoodServiceRequestDetail::join('menus', 'food_service_request_details.menu_id', '=', 'menus.id')
            ->where('menus.hotel_id', $hotel_id)
            ->select('menus.type', DB::raw('count(*) as total'))
            ->groupBy('menus.type')
            ->get();

        try {
            $statistic = [];

            foreach ($menus as $menu) {
                $statistic[$menu->type] = $menu->total;
            }

            $cash_payment = FoodServiceRequest::where('hotel_id', $hotel_id)
                ->where('payment_method', 'Cash')
                ->count();

            $scan_qr_payment = FoodServiceRequest::where('hotel_id', $hotel_id)
                ->where('payment_method', 'Scan QR')
                ->count();

            $balance = FoodServiceRequest::where('hotel_id', $hotel_id)
                ->where('payment_method', 'Scan QR')
                ->where('is_paid', 1)
                ->where('is_withdrawn', 0)
                ->sum('total');

            $withdrawn = FoodServiceRequest::where('payment_method', 'Scan QR')
                ->where('is_paid', 1)
                ->where('is_withdrawn', 1)
                ->sum('total');

            $cash_total = FoodServiceRequest::where('payment_method', 'Cash')
                ->where('is_accepted', 1)
                ->where('payment_method', 'Cash')
                ->sum('total');

            $unpaid_total = FoodServiceRequest::where('is_paid', 0)->sum('total');
            $order_total = FoodServiceRequest::sum('total');
            $undelivered_total = FoodServiceRequest::where('is_accepted', 0)->count();
            $request_used = FoodServiceRequest::count();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get food order statistic',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'statistic' => $statistic,
            'cash_payment' => $cash_payment,
            'scan_qr_payment' => $scan_qr_payment,
            'withdrawn' => $withdrawn,
            'balance' => $balance,
            'cash_total' => $cash_total,
            'unpaid_total' => $unpaid_total,
            'undelivered_total' => $undelivered_total,
            'order_total' => $order_total,
            'request_used' => $request_used,
        ]);
    }

    public function food_order_table($hotel_id)
    {
        $food_order_request = FoodServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', '!=', null)
            ->get();

        try {
            $food_order_list = [];

            foreach ($food_order_request as $order_request) {
                $is_accepted = $order_request->is_accepted;
                $total = $order_request->total;
                $payment_method = $order_request->payment_method;
                $created_at = $order_request->created_at;

                $television_id = $order_request->television_id;
                $television = Television::where('id', $television_id)->first();

                $room_number = $television->room_number;

                $food_orders = FoodServiceRequestDetail::where('food_service_request_id', $order_request->id)->get();

                $order_detail = [];

                foreach ($food_orders as $food_order) {
                    $menu_id = $food_order->menu_id;
                    $menu = Menu::where('id', $menu_id)->first();

                    $menu_name = $menu->name;
                    $menu_price = $menu->price;
                    $menu_image = $menu->image;
                    $quantity = $food_order->qty;

                    $order_detail[] = [
                        'menu_id' => $menu_id,
                        'menu_name' => $menu_name,
                        'menu_price' => $menu_price,
                        'menu_image' => $menu_image,
                        'quantity' => $quantity,
                    ];
                }

                $order_history_list[] = [
                    'id' => $order_request->id,
                    'room_number' => $room_number,
                    'order_detail' => $order_detail,
                    'is_accepted' => $is_accepted,
                    'payment_method' => $payment_method,
                    'total' => $total,
                    'created_at' => $created_at->format('Y-m-d H:i'),
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get food order table',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'order_history_list' => $order_history_list
        ]);
    }

    public function revenue($hotel_id)
    {
        $food_order_requests = FoodServiceRequest::where('hotel_id', $hotel_id)
            ->where('is_accepted', 1)
            ->where('is_paid', 1)
            ->get();

        $transaction_data = [];
        try {
            $revenue = 0;
            $qris_revenue = 0;
            $cash_revenue = 0;

            foreach ($food_order_requests as $order_request) {
                $television = Television::where('id', $order_request->television_id)->first();

                $room_number = $television->room_number;
                $guest_name = $television->guest_name;
                $total = $order_request->total;
                $payment_method = $order_request->payment_method;
                $order_made = explode(' ', $order_request->created_at);
                $order_complete = explode(' ', $order_request->updated_at);

                $transaction_data[] = [
                    'room_number' => $room_number,
                    'guest_name' => $guest_name,
                    'total' => $total,
                    'payment_method' => $payment_method,
                    'order_made' => $order_made[0] . ' ' . $order_made[1],
                    'order_complete' => $order_complete[0] . ' ' . $order_complete[1],
                ];

                $revenue = $total + $revenue;

                if (strtolower($payment_method) == 'cash') {
                    $cash_revenue = $total + $cash_revenue;
                } else if (strtolower($payment_method) == 'scan qr') {
                    $qris_revenue = $total + $qris_revenue;
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get room service request history',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'revenue' => $revenue,
            'cash_revenue' => $cash_revenue,
            'qris_revenue' => $qris_revenue,
            'transaction_data' => $transaction_data,
        ]);
    }
}
