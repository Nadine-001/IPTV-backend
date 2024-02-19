<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthController extends Controller
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $email = $request->email;
        $password = $request->password;

        try {
            $user = $this->auth->signInWithEmailAndPassword($email, $password);

            $uid = $user->firebaseUserId();
            $token = $user->idToken();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'login failed',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json([
            'email' => $email,
            'UID' => $uid,
            'token' => $token,
        ]);
    }
}
