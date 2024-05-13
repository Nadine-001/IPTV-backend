<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
