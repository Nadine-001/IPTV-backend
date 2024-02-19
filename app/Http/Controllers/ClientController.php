<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Role;
use App\Models\Television;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function add_hotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'logo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $file_name = time() . " - " . $request->logo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_logo = asset("uploads/hotels/" . $file_name);
            $request->logo->move(public_path('uploads/hotels/'), $file_name);

            Hotel::create([
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'phone' => $request->phone,
                'logo' => $path_logo,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add hotel',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel added succesfully');
    }

    public function add_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $hotel_id = null;
            if ($request->hotel_name) {
                $hotel = Hotel::where('name', $request->hotel_name)->first();
                $hotel_id = $hotel->id;
            }

            $role = Role::where('role_name', $request->role)->first();

            User::create([
                'hotel_id' => $hotel_id,
                'role_id' => $role->id,
                'email' => $request->email,
                'password' => $request->password,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add admin',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('admin added succesfully');
    }

    public function add_television(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required',
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            Television::create([
                'hotel_id' => $hotel_id,
                'room_number' => $request->room_number,
                'mac_address' => $request->mac_address,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add mac address',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('mac address added succesfully');
    }
}
