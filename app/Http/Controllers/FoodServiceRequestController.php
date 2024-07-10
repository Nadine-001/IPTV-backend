<?php

namespace App\Http\Controllers;

use App\Models\FoodServiceRequest;
use App\Models\FoodServiceRequestDetail;
use App\Models\Hotel;
use App\Models\Menu;
use App\Models\Television;
use App\Models\TempCartFoodService;
use App\Models\TempCartFoodServiceDetail;
use ElephantIO\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FoodServiceRequestController extends Controller
{
    protected $auth, $rtdb;

    public function __construct()
    {
        $this->auth = Firebase::auth();

        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->rtdb = $firebase->withDatabaseUri(env("FIREBASE_DATABASE_URL"))
            ->createDatabase();
    }

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

            $unpaid_order = FoodServiceRequest::where('television_id', $television->id)
                ->where('payment_method', 'Scan QR')
                ->where('is_paid', 0)
                ->first();

            if (!$unpaid_order) {
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
            } else {
                return response()->json([
                    'message' => 'you need to complete the payment of previous order first.'
                ], 402);
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
                'message' => 'failed to show cart',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($order_list);
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

            if (strtolower($request->payment_method) == "scan qr") {
                $order_id = date('Ymd') . strval($food_service_request->id);

                $food_service_request->update([
                    'order_id' => $order_id
                ]);

                date_default_timezone_set('Asia/Jakarta');
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

                $food_service_request->update([
                    'qr_code' => $payment_url,
                    'qr_code_expire_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
                ]);

                $data = [
                    "order_id" => $order_id,
                    "database_id" => $database_id,
                    "payment_url" => $payment_url,
                ];

                return response()->json($data);
            } else if (strtolower($request->payment_method) == "cash") {
                $food_service_request->update([
                    'is_notified' => 1,
                ]);

                $url = 'https://iptv-hms.socket.dev.mas-ts.com';
                // $url = 'http://localhost:8000';

                $options = ['client' => Client::CLIENT_4X];

                $client = Client::create($url, $options);
                $client->connect();

                $data = [
                    'hotel_id' => $hotel->id,
                    'message' => "New food order!"
                ];

                $client->emit('newFoodOrder', $data);

                $client->disconnect();
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
            'database_id' => $food_service_request->id,
        ]);
    }

    public function payment_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_request_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_service_request = FoodServiceRequest::where('id', $request->food_request_id)->first();

            $is_paid = $food_service_request->is_paid;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to get payment status',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'payment_status' => $is_paid
        ]);
    }

    public function pending_transaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();

            $food_service_request = FoodServiceRequest::where('television_id', $television->id)
                ->where('payment_method', 'Scan QR')
                ->where('is_paid', 0)
                ->first();

            $orders = FoodServiceRequestDetail::where('food_service_request_id', $food_service_request->id)->get();

            $unpaid_order = [];
            foreach ($orders as $order) {
                $menu_id = $order->menu_id;
                $menu = Menu::where('id', $menu_id)->first();

                $item_id = $order->id;
                $menu_id = $menu_id;
                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_image = $menu->image;
                $quantity = $order->qty;

                $unpaid_order[] = [
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
            DB::rollBack();
            return response()->json([
                'message' => 'failed to show QR Code',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'unpaid_order' => $unpaid_order,
            'total' => $food_service_request->total,
            'food_request_id' => $food_service_request->id
        ]);
    }

    public function show_qr_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_request_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_service_request = FoodServiceRequest::where('id', $request->food_request_id)->first();

            $payment_method = $food_service_request->payment_method;
            $is_paid = $food_service_request->is_paid;

            if (strtolower($payment_method) == 'scan qr') {
                if ($is_paid == 0) {
                    $qr_code_expire_time = $food_service_request->qr_code_expire_time;

                    date_default_timezone_set('Asia/Jakarta');
                    if ($qr_code_expire_time <= date(now())) {
                        $food_service_request->update([
                            'qr_code' => NULL,
                            'is_paid' => NULL
                        ]);

                        return response()->json([
                            'message' => 'the payment link is expired.'
                        ], 404);
                    } else {
                        $payment_link = $food_service_request->qr_code;
                        $status_code = 200;
                    }
                } else {
                    return response()->json([
                        'message' => 'this transaction has been paid.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'the payment method is cash.'
                ], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to show QR Code',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'payment_link' => $payment_link
        ], $status_code);
    }

    public function get_payment_method(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_request_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_service_request = FoodServiceRequest::where('id', $request->food_request_id)->first();

            $is_paid = $food_service_request->is_paid;
            if ($is_paid == 0) {
                $payment_method = $food_service_request->payment_method;
            } else {
                return response()->json([
                    'message' => 'this transaction has been successful, you can not change the payment method.'
                ], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to get payment method',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'payment_method' => $payment_method
        ]);
    }

    public function change_payment_method(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_request_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_service_request = FoodServiceRequest::where('id', $request->food_request_id)->first();

            $is_paid = $food_service_request->is_paid;
            if ($is_paid == 0) {
                if (strtolower($food_service_request->payment_method) == 'scan qr' && strtolower($request->payment_method) == 'cash') {
                    $food_service_request->update([
                        'payment_method' => 'Cash',
                        'order_id' => NULL,
                        'qr_code' => NULL,
                        'qr_code_expire_time' => NULL,
                    ]);

                    return response()->json([
                        'food_request_id' => $food_service_request->id,
                        'payment_method' => $food_service_request->payment_method,
                    ]);
                } else if (strtolower($food_service_request->payment_method) == 'cash' && strtolower($request->payment_method) == 'scan qr') {
                    $orders = FoodServiceRequestDetail::where('food_service_request_id', $request->food_request_id)->get();

                    $item_details = [];
                    foreach ($orders as $order) {
                        $menu_id = $order->menu_id;
                        $menu = Menu::where('id', $menu_id)->first();

                        $item_details[] = [
                            "id" => strval($menu_id),
                            "name" => $menu->name,
                            "price" => $menu->price,
                            "quantity" => $order->qty
                        ];
                    }

                    $order_id = date('Ymd') . strval($food_service_request->id);

                    $food_service_request->update([
                        'payment_method' => 'Scan QR',
                        'order_id' => $order_id
                    ]);

                    date_default_timezone_set('Asia/Jakarta');
                    $response = Http::withHeaders([
                        'Accept: application/json',
                        'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1RS1F1dHZoaUFtUW1CeTktTjlKb0ZRaEM6',
                        'Content-Type' => 'application/json',
                    ])
                        ->post('https://api.sandbox.midtrans.com/v1/payment-links', [
                            "transaction_details" => [
                                "order_id" => $order_id,
                                "gross_amount" => $food_service_request->total,
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
                    $database_id = strval($request->food_request_id);
                    $payment_url = $data["payment_url"];

                    $food_service_request->update([
                        'qr_code' => $payment_url,
                        'qr_code_expire_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
                    ]);

                    $data = [
                        "order_id" => $order_id,
                        "database_id" => $database_id,
                        "payment_url" => $payment_url,
                    ];

                    return response()->json($data);
                } else {
                    return response()->json([
                        'message' => 'nothing\'s changed.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'this transaction has been successful, you can not change the payment method.'
                ], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to update payment method',
                'errors' => $th->getMessage()
            ], 400);
        }
    }

    public function notification(Request $request)
    {
        try {
            $server_key = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $server_key);

            $order_id = explode('-', $request->order_id);

            if ($hashed == $request->signature_key) {
                if ($request->transaction_status == 'settlement') {
                    $food_service_request = FoodServiceRequest::where('order_id', $order_id[0])->first();

                    $food_service_request->update([
                        'is_paid' => 1,
                        'qr_code' => NULL,
                        'qr_code_expire_time' => NULL,
                        'is_notified' => 1,
                    ]);

                    $url = 'https://iptv-hms.socket.dev.mas-ts.com';
                    // $url = 'http://localhost:8000';

                    $options = ['client' => Client::CLIENT_4X];

                    $client = Client::create($url, $options);
                    $client->connect();

                    $data = [
                        'hotel_id' => $food_service_request->hotel_id,
                        'message' => "New food order!"
                    ];

                    $client->emit('newFoodOrder', $data);

                    $client->disconnect();

                    return response()->json('OK');
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'failed to delete QR Code',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('Something went wrong.', 400);
    }

    public function faspay_notification(Request $req)
    {
        try {
            $hashed = hash('sha1', hash('md5', 'bot' . $req->merchant_id . 'p@ssw0rd' . $req->bill_no . $req->payment_status_code));

            if ($hashed == $req->signature) {
                if ($req->payment_status_code == 2) {

                    $transaction = $this->rtdb->getReference($req->trx_id);

                    $transaction->update([
                        'status' => 'paid'
                    ]);

                    date_default_timezone_set('Asia/Jakarta');

                    return response()->json([
                        'respone' => "Payment Notification",
                        'trx_id' => $req->trx_id,
                        'merchant_id' => $req->merchant_id,
                        'merchant' => $req->merchant,
                        'bill_no' => $req->bill_no,
                        'response_code' => "00",
                        'response_desc' => "Success",
                        'response_date' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    return response()->json([
                        'respone' => "Payment Notification",
                        'trx_id' => $req->trx_id,
                        'merchant_id' => $req->merchant_id,
                        'merchant' => $req->merchant,
                        'bill_no' => $req->bill_no,
                        'response_desc' => "Failed",
                        'response_date' => date('Y-m-d H:i:s'),
                    ], 417);
                }
            } else {
                return response()->json([
                    'respone' => "Payment Notification",
                    'trx_id' => $req->trx_id,
                    'merchant_id' => $req->merchant_id,
                    'merchant' => $req->merchant,
                    'bill_no' => $req->bill_no,
                    'response_desc' => "Signature don't match",
                    'response_date' => date('Y-m-d H:i:s'),
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get notification',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('Something went wrong.', 400);
    }

    public function return_page(Request $request)
    {
        $status = $request->status;

        if ($status == '2') {
            $message = 'Thank You! Your Transaction Success';
        } else {
            $message = 'Sorry! Your Transaction Failed';
        }

        return view('merchant_return_page', ['message' => $message]);
    }
}
