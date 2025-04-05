<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProfileOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $userIdFromRoute = $request->route('userId') ?? Auth::id();

        if($user->id !== (int) $userIdFromRoute){
            return response()->json([
                'message' => 'Unauthorized: You can only modify your own profile',
            ], 403);
        }
        return $next($request);
    }
}
