<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Role;
use App\Models\Television;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ClientController extends Controller
{
    protected $auth, $rtdb;
    // $firestore;

    public function __construct()
    {
        $this->auth = Firebase::auth();

        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->rtdb = $firebase->withDatabaseUri(env("FIREBASE_DATABASE_URL"))
            ->createDatabase();

        // $this->firestore = $firebase->createFirestore()
        //     ->database();
    }

    public function add_hotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'contact_person' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $hotel = Hotel::create([
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'phone' => $request->phone,
                'cp' => $request->contact_person,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add hotel',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_id' => $hotel->id
        ]);
    }

    public function add_admin(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'role' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $email = $request->email;
        $password = $request->password;

        try {
            $role = Role::where('role_name', $request->role)->first();

            User::create([
                'hotel_id' => $hotel_id,
                'role_id' => $role->id,
                'email' => $email,
                'password' => Hash::make($request->password),
            ]);

            $new_user = $this->auth->createUserWithEmailAndPassword($email, $password);
            $uid = $new_user->uid;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add admin',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'UID' => $uid,
            'email' => $email,
            'role' => $request->role,
        ]);
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

    public function hotel_data_list()
    {
        $hotels = Hotel::all();

        try {
            $hotels_data = [];

            foreach ($hotels as $hotel) {
                $hotel_name = $hotel->name;
                $hotel_city = $hotel->city;

                $users = $hotel->user;

                $admin_list = [];
                foreach ($users as $user) {
                    $admin_email = $user->email;
                    $role_id = $user->role_id;

                    $role = Role::where('id', $role_id)->first();
                    $admin_role = $role->role_name;

                    $admin_list[] = [
                        'admin_email' => $admin_email,
                        'admin_role' => $admin_role,
                    ];
                }

                $hotels_data[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel_name,
                    'hotel_city' => $hotel_city,
                    'admin_list' => $admin_list,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel data list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotels_data' => $hotels_data
        ]);
    }

    public function hotel_data($hotel_id)
    {
        $hotel = Hotel::where('id', $hotel_id)->first();

        try {
            $hotel_name = $hotel->name;
            $hotel_address = $hotel->address;
            $hotel_city = $hotel->city;
            $hotel_logo = $hotel->logo;
            $hotel_phone = $hotel->phone;
            $hotel_cp = $hotel->cp;
            $hotel_status = $hotel->is_active;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_id' => $hotel_id,
            'hotel_name' => $hotel_name,
            'hotel_address' => $hotel_address,
            'hotel_city' => $hotel_city,
            'hotel_phone' => $hotel_phone,
            'hotel_cp' => $hotel_cp,
            'hotel_logo' => $hotel_logo,
            'hotel_status' => $hotel_status,
        ]);
    }

    public function update_hotel(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'contact_person' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $hotel = Hotel::where('id', $hotel_id)->first();

        try {
            $hotel->update([
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'phone' => $request->phone,
                'cp' => $request->contact_person,
                'is_active' => $request->status,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update hotel data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel data updated successfully');
    }

    public function update_hotel_logo(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $hotel = Hotel::where('id', $hotel_id)->first();

        try {
            $file_name = time() . " - " . $request->logo->getClientOriginalName();
            $file_name = str_replace(' ', '', $file_name);
            $path_logo = asset("uploads/hotels/" . $file_name);
            $request->logo->move(public_path('uploads/hotels/'), $file_name);

            $hotel->update([
                'logo' => $path_logo,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update hotel logo',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('hotel logo updated successfully');
    }

    public function television_data($hotel_id)
    {
        $televisions = Television::where('hotel_id', $hotel_id)->get();

        try {
            $mac_address_list = [];
            foreach ($televisions as $television) {
                $television_id = $television->id;
                $room_number = $television->room_number;
                $mac_address = $television->mac_address;

                $mac_addresses = explode(',', $mac_address);

                $mac_address_list[] = [
                    'television_id' => $television_id,
                    'room_number' => $room_number,
                    'mac_addresses' => $mac_addresses,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get television list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address_list' => $mac_address_list
        ]);
    }

    public function get_television(Request $request, $television_id)
    {
        try {
            $television = Television::where('id', $television_id)->first();

            $room_number = $television->room_number;
            $mac_address = $television->mac_address;
            $status = $television->is_active;

            $mac_addresses = explode(',', $mac_address);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get television data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'room_number' => $room_number,
            'mac_addresses' => $mac_addresses,
            'status' => $status,
        ]);
    }

    public function update_television(Request $request, $television_id)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required',
            'mac_address' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $television = Television::where('id', $television_id)->first();

            $television->update([
                'room_number' => $request->room_number,
                'mac_address' => $request->mac_address,
                'is_active' => $request->status,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update television data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('television data updated succesfully');
    }

    public function admin_list($hotel_id)
    {
        $users = User::where('hotel_id', $hotel_id)->get();

        $admin_list = [];
        try {
            foreach ($users as $user) {
                $admin_id = $user->id;
                $admin_email = $user->email;
                $admin_role = $user->role->role_name;
                // $admin_password = $user->password;

                $admin_list[] = [
                    'admin_id' => $admin_id,
                    'admin_email' => $admin_email,
                    'admin_role' => $admin_role,
                    // 'admin_password' => $admin_password,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get admin list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'admin_list' => $admin_list
        ]);
    }

    public function admin_data(Request $request, $admin_id)
    {
        $user = User::where('id', $admin_id)->first();

        try {
            $admin_email = $user->email;
            $admin_role = $user->role->role_name;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update admin data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'admin_email' => $admin_email,
            'admin_role' => $admin_role,
        ]);
    }

    public function update_admin(Request $request, $admin_id)
    {
        $user = User::where('id', $admin_id)->first();

        try {
            $user->update([
                'email' => $request->email,
                // 'password' => $request->admin['password'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update admin data',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json('admin data updated successfully');
    }

    public function hotel_list()
    {
        $hotels = Hotel::where('is_active', 1)->get();

        try {
            $hotel_list = [];
            foreach ($hotels as $hotel) {
                $hotel_id = $hotel->id;
                $hotel_name = $hotel->name;
                $hotel_logo = $hotel->logo;
                $hotel_city = $hotel->city;

                if (!isset($hotel_list[$hotel_city])) {
                    $hotel_list[$hotel_city] = [
                        'hotel_city' => $hotel_city,
                        'hotels' => []
                    ];
                }

                $hotel_list[$hotel_city]['hotels'][] = [
                    'hotel_id' => $hotel_id,
                    'hotel_name' => $hotel_name,
                    'hotel_logo' => $hotel_logo,
                ];
            }

            $hotel_list = array_values($hotel_list);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_list' => $hotel_list
        ]);
    }

    public function television_list($hotel_id)
    {
        $televisions = Television::where('hotel_id', $hotel_id)
            ->where('is_active', 1)
            ->get();

        try {
            $mac_address_list = [];
            foreach ($televisions as $television) {
                $room_number = $television->room_number;
                $mac_address = $television->mac_address;

                $mac_addresses = explode(',', $mac_address);

                $mac_address_list[] = [
                    'room_number' => $room_number,
                    'mac_addresses' => $mac_addresses,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get television list',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'mac_address_list' => $mac_address_list
        ]);
    }
}
