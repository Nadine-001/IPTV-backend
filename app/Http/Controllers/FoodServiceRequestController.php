<?php

namespace App\Http\Controllers;

use App\Models\FoodServiceRequest;
use App\Models\FoodServiceRequestDetail;
use App\Models\Hotel;
use App\Models\Menu;
use App\Models\Television;
use App\Models\TempCartFoodService;
use App\Models\TempCartFoodServiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class FoodServiceRequestController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'menu_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            $temp_cart = TempCartFoodService::where('television_id', $television->id)
                ->where('hotel_id', $hotel->id)
                ->where('menu_id', $request->menu_id)
                ->first();

            if ($temp_cart) {
                $temp_cart->update([
                    'qty' => $temp_cart->qty + 1,
                ]);
            } else {
                TempCartFoodService::create([
                    'hotel_id' => $hotel->id,
                    'television_id' => $television->id,
                    'menu_id' => $request->menu_id,
                    'qty' => 1,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add to cart',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address' => $request->mac_address,
            'menu_id' => $request->menu_id,
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
            $carts = TempCartFoodService::where('television_id', $television->id)->get();

            $order_list = [];
            foreach ($carts as $cart) {
                $menu = Menu::where('id', $cart->menu_id)->first();

                $item_id = $cart->id;
                $menu_id = $menu->id;
                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_image = $menu->image;
                $quantity = $cart->qty;

                $order_list[] = [
                    'item_id' => $item_id,
                    'menu_id' => $menu_id,
                    'menu_name' => $menu_name,
                    'menu_description' => $menu_description,
                    'menu_price' => $menu_price,
                    'menu_image' => $menu_image,
                    'quantity' => $quantity,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to place order',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'order_list' => $order_list,
        ]);
    }

    public function increase_item(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // DB::beginTransaction();
        try {
            $item = TempCartFoodService::where('id', $item_id)->first();

            // $quantity = $item->qty;
            $item->update([
                'qty' => $item->qty + 1,
            ]);

            // $menu_id = $item->menu_id;
            // $menu = Menu::where('id', $menu_id)->first();

            // $menu_price = $menu->price;

            // $item_summary = TempCartFoodService::where('id', $item->temp_cart_food_service_id)->first();

            // $total = $item_summary->total;
            // $item_summary->update([
            //     'total' => $total + $menu_price,
            // ]);
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
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

        // DB::beginTransaction();
        try {
            $item = TempCartFoodService::where('id', $item_id)->first();

            $item->update([
                'qty' => $item->qty - 1,
            ]);

            if ($item->qty < 1) {
                $deleted = $item->delete();

                if (!$deleted) {
                    return response()->json([
                        "message" => "failed to delete item"
                    ], 500);
                }
            }

            // $menu_id = $item->menu_id;
            // $menu = Menu::where('id', $menu_id)->first();

            // $menu_price = $menu->price;

            // $item_summary = TempCartFoodService::where('id', $item->temp_cart_food_service_id)->first();

            // $total = $item_summary->total;
            // $item_summary->update([
            //     'total' => $total - $menu_price,
            // ]);
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json([
                'message' => 'failed to decrease item',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('item decreased succesfully');
    }

    public function delete_order(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // DB::beginTransaction();
        try {
            $item = TempCartFoodService::where('id', $item_id)->first();
            $deleted = $item->delete();

            if (!$deleted) {
                return response()->json([
                    "message" => "failed to delete item"
                ], 500);
            }

            // $menu_id = $item->menu_id;
            // $menu = Menu::where('id', $menu_id)->first();

            // $menu_price = $menu->price;

            // $item_summary = TempCartFoodService::where('id', $item->temp_cart_food_service_id)->first();

            // $total = $item_summary->total;
            // $item_summary->update([
            //     'total' => $total - $menu_price,
            // ]);

            // $item = TempCartFoodServiceDetail::where('id', $item_id)->first();
            // if (!$item) {
            // $item_parent = TempCartFoodService::where('id', $item->temp_cart_food_service_id)->first();
            //     $deleted = $item_summary->delete();

            //     if (!$deleted) {
            //         return response()->json([
            //             "message" => "failed to delete item"
            //         ], 500);
            //     }
            // }
            // DB::commit();
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json([
                'message' => 'failed to delete item',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('item deleted succesfully');
    }

    public function food_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'orders' => 'required|array',
            'total' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            $food_service_request = FoodServiceRequest::create([
                'hotel_id' => $hotel->id,
                'television_id' => $television->id,
                'total' => $request->total,
                'payment_method' => $request->payment_method,
                'is_paid' => 0,
            ]);

            $item_details = [];
            foreach ($request->orders as $order) {
                FoodServiceRequestDetail::create([
                    'food_service_request_id' => $food_service_request->id,
                    'menu_id' => $order['menu_id'],
                    'qty' => $order['quantity'],
                ]);

                $item = TempCartFoodService::where('id', $order['item_id'])->first();
                $deleted = $item->delete();

                if (!$deleted) {
                    return response()->json([
                        "message" => "failed to delete item"
                    ], 500);
                }

                $menu = Menu::where('id', $order['menu_id'])->first();

                $item_details[] = [
                    "id" => strval($order['menu_id']),
                    "name" => $menu->name,
                    "price" => $menu->price,
                    "quantity" => $order['quantity']
                ];
            }

            DB::commit();

            // $item_parent = TempCartFoodService::where('id', $item->temp_cart_food_service_id)->first();
            // $deleted = $item_parent->delete();

            // if (!$deleted) {
            //     return response()->json([
            //         "message" => "failed delete item"
            //     ], 500);
            // }

            date_default_timezone_set('Asia/Jakarta');
            if (strtolower($request->payment_method) == "scan qr") {
                $order_id = date('Ymd') . strval($food_service_request->id);

                $food_service_request->update([
                    'order_id' => $order_id
                ]);

                $response = Http::withHeaders([
                    'Accept: application/json',
                    'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1RS1F1dHZoaUFtUW1CeTktTjlKb0ZRaEM6',
                    'Content-Type' => 'application/json',
                ])
                    ->post('https://api.sandbox.midtrans.com/v1/payment-links', [
                        "transaction_details" => [
                            "order_id" => $order_id,
                            "gross_amount" => $request->total,
                            "payment_link_id" => $order_id
                        ],
                        "customer_required" => false,
                        "usage_limit" => 1,
                        "expiry" => [
                            "start_time" => date('Y-m-d H:i O'),
                            "duration" => 1,
                            "unit" => "days"
                        ],
                        "enabled_payments" => [
                            "gopay",
                            "cimb_clicks",
                            "bca_klikbca",
                            "bca_klikpay",
                            "bri_epay",
                            "telkomsel_cash",
                            "echannel",
                            "permata_va",
                            "other_va",
                            "bca_va",
                            "bni_va",
                            "bri_va",
                            "danamon_online",
                            "shopeepay"
                        ],
                        "item_details" => $item_details
                    ]);

                $data = $response->json();

                $order_id = $data["order_id"];
                $database_id = strval($food_service_request->id);
                $payment_url = $data["payment_url"];

                $data = [
                    "order_id" => $order_id,
                    "database_id" => $database_id,
                    "payment_url" => $payment_url,
                ];

                return $data;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to place order',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address' => $request->mac_address,
            'orders' => $request->orders,
            'total' => $request->total,
            'payment_method' => $request->payment_method,
        ]);
    }

    public function notification(Request $request)
    {
        $server_key = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $server_key);

        $order_id = explode('-', $request->order_id);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'settlement') {
                $food_service_request = FoodServiceRequest::where('order_id', $order_id[0])->first();
                $food_service_request->update([
                    'is_paid' => 1
                ]);
            }
        }

        return response()->json('OK');
    }
}
