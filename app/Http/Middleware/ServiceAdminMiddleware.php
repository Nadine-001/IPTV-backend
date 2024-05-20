<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Symfony\Component\HttpFoundation\Response;

class ServiceAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $response = (object) [];
        // $token = $request->bearerToken();
        // $response->token = $token;

        // $auth = app('firebase.auth');
        // try {
        //     $verifiedIdToken = $auth->verifyIdToken($token);
        // } catch (FailedToVerifyToken $e) {
        //     return response()->json([
        //         'message' => 'invalid token',
        //         'error' => $e->getMessage()
        //     ]);
        // }

        // $email = $verifiedIdToken->claims()->get('email');

        // $admin = User::where('email', $email)->first();
        // $role_id = $admin->role_id;

        if ($request->user() && $request->user()->role_id == 3 || $request->user() && $request->user()->role_id >= 99) {
            return $next($request);
        }

        return response()->json(
            [
                'error' => 'Access Denied'
            ],
            403
        );
    }
}
