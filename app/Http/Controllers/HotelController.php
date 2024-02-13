<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelFacilities;
use App\Models\Room;
use App\Models\Television;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class HotelController extends Controller
{
    protected $auth, $rtdb, $firestore;
    public function __construct()
    {
        $this->auth = Firebase::auth();

        $firebase = (new Factory)
        ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->rtdb = $firebase->withDatabaseUri(env("FIREBASE_DATABASE_URL"))
        ->createDatabase();

        $this->firestore = $firebase->createFirestore()
        ->database();
    }

    public function home(Request $request, $hotel_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id);

            $hotel_wifi = $hotel->qr_code_wifi;
            $hotel_logo = $hotel->logo;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get home content',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_wifi' => $hotel_wifi,
            'hotel_logo' => $hotel_logo,
        ]);
    }

    public function hotel_greeting(Request $request, $hotel_id, $room_id) {
        try {
            $hotel = Hotel::findOrFail($hotel_id);
            $television = Television::where('room_id', $room_id)->first();

            $guest_name = $television->guest_name;
            $hotel_greeting = $hotel->greeting;
            $hotel_logo = $hotel->logo;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel greeting',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'guest_name' => $guest_name,
            'hotel_greeting' => $hotel_greeting,
            'hotel_logo' => $hotel_logo,
        ]);
    }

    public function hotel_about(Request $request, $hotel_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id);

            $hotel_name = $hotel->name;
            $hotel_class = $hotel->class;
            $hotel_about = $hotel->about;
            $hotel_check_in = $hotel->check_in;
            $hotel_check_out = $hotel->check_out;
            $hotel_photo = $hotel->photo;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel about',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_name' => $hotel_name,
            'hotel_class' => $hotel_class,
            'hotel_about' => $hotel_about,
            'hotel_check_in' => $hotel_check_in,
            'hotel_check_out' => $hotel_check_out,
            'hotel_photo' => $hotel_photo,
        ]);
    }


    public function hotel_location(Request $request, $hotel_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id);

            $hotel_address = $hotel->address;
            $hotel_phone = $hotel->phone;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel location',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'hotel_address' => $hotel_address,
            'hotel_phone' => $hotel_phone,
        ]);
    }

    public function hotel_facilites(Request $request, $hotel_id)
    {
        try {
            // $hotel = Hotel::findOrFail($hotel_id);
            $facilities = HotelFacilities::where('hotel_id', $hotel_id)->get();

            $facilities_data = [];

            foreach ($facilities as $facility) {
                $facility_name = $facility->name;
                $facility_description = $facility->description;
                $facility_image = $facility->image;

                $facilities_data[] = [
                    'facility_name' => $facility_name,
                    'facility_description' => $facility_description,
                    'facility_image' => $facility_image,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get hotel facilites',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json($facilities_data);
    }
}
