<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthController extends Controller
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

    public function sign_up(Request $request, $hotel_id)
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
                'message' => 'sign up failed',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json([
            'UID' => $uid,
            'email' => $email,
            'role' => $request->role,
        ]);
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
            $admin = User::where('email', $email)->first();
            $hotel_id = $admin->hotel_id;

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
            'hotel_id' => $hotel_id,
        ]);
    }

    public function profile(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $verifiedIdToken = $this->auth->verifyIdToken($token);
            $email = $verifiedIdToken->claims()->get('email');
            $user = User::where('email', $email)->first();
            $role_id = $user->role_id;
            $role = Role::where('id', $role_id)->first();

            $role_name = $role->role_name;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to get profile',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json([
            'email' => $email,
            'role_name' => $role_name,
        ]);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $token = $request->bearerToken();
            $verifiedIdToken = $this->auth->verifyIdToken($token);
            $email = $verifiedIdToken->claims()->get('email');
            $user = User::where('email', $email)->first();

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            $uid = $verifiedIdToken->claims()->get('sub');
            $this->auth->changeUserPassword($uid, $request->password);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update password',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('password updated successfully');
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $email = $request->email;

        try {
            $this->auth->sendPasswordResetLink($email);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'logout failed',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('email sent');
    }

    public function logout(Request $request)
    {
        try {
            $this->auth->revokeRefreshTokens($this->getUid($request));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'logout failed',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('logout success');
    }

    public function getUid(Request $request)
    {
        $token = $request->bearerToken();
        $verifiedIdToken = $this->auth->verifyIdToken($token);
        $uid = $verifiedIdToken->claims()->get('sub');

        return $uid;
    }
}
