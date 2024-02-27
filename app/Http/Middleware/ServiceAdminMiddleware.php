<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if ($request->user() && ($request->user()->role_id == 3 || $request->user()->role_id >= 99)) {
            return $next($request);
        }

        return response()->json([
                'error' => 'Access Denied'
            ],
            403
        );    }
}
