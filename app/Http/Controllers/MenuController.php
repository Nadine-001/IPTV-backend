<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Menu;
use App\Models\MenuType;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function ads_lips_menu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            $order_food_intro = $hotel->order_food_intro;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'order_food_intro' => $order_food_intro,
        ]);
    }

    public function menu_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
            'menu_type_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();
            $menu_type = MenuType::where('id', $request->menu_type_id)->first();
            $menus = Menu::where('hotel_id', $hotel->id)
                ->where('type', $menu_type->type)
                ->get();

            $menu_data = [];

            foreach ($menus as $menu) {
                $menu_id = $menu->id;
                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_image = $menu->image;

                $menu_data[] = [
                    'menu_id' => $menu_id,
                    'menu_name' => $menu_name,
                    'menu_description' => $menu_description,
                    'menu_price' => $menu_price,
                    'menu_image' => $menu_image,
                ];
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($menu_data);
    }

    // public function list_menu(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'mac_address' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }

    //     try {
    //         $television = Television::where('mac_address', $request->mac_address)->first();
    //         $hotel = Hotel::where('id', $television->hotel_id)->first();
    //         $menus = Menu::where('hotel_id', $hotel->id)->get();

    //         $menu_data = [];

    //         foreach ($menus as $menu) {
    //             $menu_id = $menu->id;
    //             $menu_type = $menu->type;
    //             $menu_name = $menu->name;
    //             $menu_description = $menu->description;
    //             $menu_price = $menu->price;
    //             $menu_image = $menu->image;

    //             $menu_type_id = MenuType::where('type', strtolower($menu_type))->first();

    //             $menu_data[] = [
    //                 'menu_id' => $menu_id,
    //                 'menu_type' => $menu_type,
    //                 'menu_type_id' => $menu_type_id->id,
    //                 'menu_name' => $menu_name,
    //                 'menu_description' => $menu_description,
    //                 'menu_price' => $menu_price,
    //                 'menu_image' => $menu_image,
    //             ];
    //         }            
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'failed to get menu',
    //             'errors' => $th->getMessage()
    //         ], 400);
    //     }

    //     return response()->json($menu_data);
    // }

    public function menu_type(Request $request) {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();

            $menu_types = MenuType::where('hotel_id', $hotel->id)
                ->where('is_deleted', 0)
                ->get();

            $menu_type = [];
            foreach ($menu_types as $types) {
                $type = $types->type;
                $image = $types->image;

                $menu_type[] = [
                    'menu_type_id' => $types->id,
                    'menu_type' => $type,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get menu type list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($menu_type);
    }

    // public function qr_code(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'mac_address' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }

    //     try {
    //         $television = Television::where('mac_address', $request->mac_address)->first();
    //         $hotel = Hotel::where('id', $television->hotel_id)->first();

    //         $qr_code_payment = $hotel->qr_code_payment;
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'failed to get QR code',
    //             'errors' => $th->getMessage()
    //         ], 400);
    //     }

    //     return response()->json([
    //         'qr_code_payment' => $qr_code_payment,
    //     ]);
    // }

    public function payment_status(Request $request) {
        $validator = Validator::make($request->all(), [
            'database_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $food_service = FoodServiceRequest::where('id', $request->database_id);
            $food_service->update([
                'is_paid' => 1
            ]);

            // $response = Http::withHeaders([
            //     'Accept' => 'application/json',
            //     'Authorization' => 'Basic U0ItTWlkLXNlcnZlci1RS1F1dHZoaUFtUW1CeTktTjlKb0ZRaEM6',
            //     'Content-Type' => 'application/json',
            // ])
            // ->get('https://api.sandbox.midtrans.com/v2/payments/20240486');

            // $data = $response->json();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get payment status',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($data);
    }
}
