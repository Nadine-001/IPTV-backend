<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Menu;
use App\Models\Television;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function menu_list(Request $request) {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();
            $menus = Menu::where('hotel_id', $hotel->id)->get();

            $order_food_intro = $hotel->order_food_intro;
            $menu_data = [];

            foreach ($menus as $menu) {
                $menu_type = $menu->type;
                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_rating = $menu->rating;
                $menu_image = $menu->image;

                $menu_data[] = [
                    'menu_type' => $menu_type,
                    'menu_name' => $menu_name,
                    'menu_description' => $menu_description,
                    'menu_price' => $menu_price,
                    'menu_rating' => $menu_rating,
                    'menu_image' => $menu_image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get menu',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'order_food_intro' => $order_food_intro,
            'menu_data' => $menu_data,
        ]);
    }

    public function qr_code(Request $request) {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $television = Television::where('mac_address', $request->mac_address)->first();
            $hotel = Hotel::where('id', $television->hotel_id)->first();
            
            $qr_code_payment = $hotel->qr_code_payment;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get QR code',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'qr_code_payment' => $qr_code_payment,
        ]);
    }
}
