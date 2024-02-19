<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Menu;

class MenuController extends Controller
{
    public function menu_list($hotel_id) {
        try {
            $hotel = Hotel::where('id', $hotel_id)->first();
            $menus = Menu::where('hotel_id', $hotel_id)->get();

            $order_food_intro = $hotel->order_food_intro;
            $menus_data = [];

            foreach ($menus as $menu) {
                $menu_type = $menu->type;
                $menu_name = $menu->name;
                $menu_description = $menu->description;
                $menu_price = $menu->price;
                $menu_rating = $menu->rating;
                $menu_image = $menu->image;

                $menus_data[] = [
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
            'menus_data' => $menus_data,
        ]);
    }

    public function qr_code($hotel_id) {
        try {
            $hotel = Hotel::where('id', $hotel_id)->first();

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
