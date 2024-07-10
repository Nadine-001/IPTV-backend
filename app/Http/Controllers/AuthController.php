<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Role;
use App\Models\User;
use ElephantIO\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function sign_up(Request $request)
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

            $new_user = $this->auth->createUserWithEmailAndPassword($email, $password);
            $uid = $new_user->uid;

            User::create([
                'role_id' => $role->id,
                'email' => $email,
                'password' => Hash::make($request->password),
                'uid' => $uid,
            ]);
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
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $admin = User::where('email', $email)->first();

            $token = $admin->createToken('auth_token')->plainTextToken;

            $hotel_id = $admin->hotel_id;

            $user = $this->auth->signInWithEmailAndPassword($email, $password);

            $uid = $user->firebaseUserId();
            $role_id = $admin->role->id;
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
            'role_id' => $role_id,
        ]);
    }

    public function profile(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::user()->id);

            $email = $user->email;
            $role_name = $user->role->role_name;

            // $token = $request->bearerToken();
            // $verifiedIdToken = $this->auth->verifyIdToken($token);
            // $email = $verifiedIdToken->claims()->get('email');
            // $user = User::where('email', $email)->first();
            // $role_id = $user->role_id;
            // $role = Role::where('id', $role_id)->first();

            // $role_name = $role->role_name;
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

    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        $email = $request->email;

        try {
            $this->auth->sendPasswordResetLink($email);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to send email',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('email sent');
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        try {
            $user = User::findOrFail(Auth::user()->id);

            // $token = $request->bearerToken();
            // $verifiedIdToken = $this->auth->verifyIdToken($token);
            // $email = $verifiedIdToken->claims()->get('email');
            // $user = User::where('email', $email)->first();

            $old_password = $user->password;
            if (password_verify($request->old_password, $old_password)) {
                $user->update([
                    'password' => Hash::make($request->new_password),
                ]);

                // $uid = $verifiedIdToken->claims()->get('sub');
                $uid = $user->uid;
                $this->auth->changeUserPassword($uid, $request->new_password);
            } else {
                return response()->json('old password does not match');
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed to update password',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('password updated successfully');
    }

    public function logout(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::user()->id);
            $user->tokens()->delete();

            $role_id = $user->role_id;
            $hotel_id = $user->hotel_id;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'logout failed',
                'errors' => $th->getMessage()
            ], 401);
        }

        return response()->json('logout success');
    }
}
