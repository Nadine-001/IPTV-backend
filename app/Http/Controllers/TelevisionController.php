<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class TelevisionController extends Controller
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

    public function add_channel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'required',
            'channel_name' => 'required',
            'channel_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $streams = $this->rtdb->getReference('streams')->getValue();
            $channel_count = is_array($streams) ? count($streams) : 0;

            $channel_data = [
                'id' => $request->channel_id,
                'name' => $request->channel_name,
                'url' => $request->channel_url,
            ];

            $this->rtdb->getReference('streams/' . $channel_count + 1)
                ->set($channel_data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add channel',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'channel_name' => $request->channel_name,
            'channel_url' => $request->channel_url,
        ]);
    }

    public function add_package(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required',
            'channel_list' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $packages = $this->rtdb->getReference('packages')->getValue();
            $package_count = is_array($packages) ? count($packages) : 0;

            $package_data = [
                'package_name' => $request->package_name,
                'status' => 1,
                'channels' => []
            ];

            $package_data['channels'] = explode(',', $request->channel_list);

            $this->rtdb->getReference('packages/' . $package_count + 1)
                ->set($package_data);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to add package',
                'errors' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'package_name' => $request->package_name,
            'channel_list' => $package_data,
        ]);
    }

    public function channel()
    {
        $streams = $this->rtdb->getReference('streams')->getValue();

        $channel = [];
        foreach ($streams as $key => $data) {
            if (!is_null($data)) {
                $channel[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'url' => $data['url'],
                ];
            }
        }

        return response()->json($channel);
    }

    public function package_list()
    {
        $packages = $this->rtdb->getReference('packages')->getValue();

        $package_list = [];
        foreach ($packages as $package_key => $package_data) {
            if (!is_null($package_data) && is_array($package_data)) {
                $package_list[] = [
                    'package_id' => $package_key,
                    'package_name' => $package_data['package_name'],
                    'channel_list' => $package_data['channels'],
                ];
            }
        }

        return response()->json([
            'package_list' => $package_list
        ]);
    }

    public function package_detail($package_id)
    {
        $packages = $this->rtdb->getReference('packages/' . $package_id);
        $package_data = $packages->getValue();

        if ($package_data) {
            return response()->json([
                'package_id' => $package_id,
                'package_data' => $package_data,
            ]);
        } else {
            return response()->json([
                'message' => 'package not found',
                'package_id' => $package_id,
            ], 404);
        }
    }

    public function subscription_list()
    {
        $hotels = Hotel::all();

        try {
            $subscription_list = [];
            $current_date = new \DateTime();

            foreach ($hotels as $hotel) {
                $hotel_name = $hotel->name;
                $package = $hotel->subscription;
                $duration = $hotel->subscription_duration;
                $start_date = new \DateTime($hotel->subscription_start);

                try {
                    $end_date = clone $start_date;
                    $end_date->modify('+' . $duration);

                    $interval = $current_date->diff($end_date);
                    $remaining_days = $interval->format('%r%a');

                    $status = ($remaining_days < 0) ? 'inactive' : 'active';
                } catch (\Throwable $th) {
                    $status = null;
                    $end_date = null;
                    $remaining_days = null;
                }

                $subscription_list[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel_name,
                    'package' => $package,
                    'start_date' => $hotel->subscription_start,
                    'end_date' => $end_date ? $end_date->format('Y-m-d') : null,
                    'remaining_days' => $remaining_days ? max(0, $remaining_days) : null,
                    'status' => $status,
                ];
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get subscription list',
                'errors' => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            'subscription_list' => $subscription_list,
        ]);
    }

    public function set_package(Request $request, $hotel_id)
    {
        $validator = Validator::make($request->all(), [
            'package' => 'required',
            'duration' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $hotel = Hotel::where('id', $hotel_id)->first();

        try {
            date_default_timezone_set('Asia/Jakarta');

            $hotel->update([
                'subscribe' => $request->package,
                'subscription_start' => date('Y-m-d'),
                'subscription_duration' => $request->duration,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to set package',
                'errors' => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            'subscribe' => $request->package,
            'subscription_start' => date('Y-m-d'),
            'subscription_duration' => $request->duration,
        ]);
    }

    public function package_update(Request $request, $package_id)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required',
            'channel_list' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $packages = $this->rtdb->getReference('packages/' . $package_id);
            $package_data = $packages->getValue();

            if ($package_data) {
                $package_data = [
                    'package_name' => $request->package_name,
                    'channels' => []
                ];

                $package_data['channels'] = explode(',', $request->channel_list);

                $packages->set($package_data);
            } else {
                return response()->json([
                    'message' => 'package not found',
                    'package_id' => $package_id,
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update package',
                'errors' => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            'package_id' => $package_id,
            'package_data' => $package_data,
        ]);
    }

    public function package_delete($package_id)
    {
        try {
            $packages = $this->rtdb->getReference('packages/' . $package_id);
            $package_data = $packages->getValue();

            if ($package_data) {
                $package_data['status'] = 0;

                $packages->set($package_data);
            } else {
                return response()->json([
                    'message' => 'package not found',
                    'package_id' => $package_id,
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update package status',
                'errors' => $th->getMessage(),
            ], 400);
        }

        return response()->json([
            'package_id' => $package_id,
            'package_data' => $package_data,
        ]);
    }
}
