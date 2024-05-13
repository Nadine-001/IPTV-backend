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
}
