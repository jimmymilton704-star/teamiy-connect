<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = User::query()
            ->activeEmployee()
            ->where('remember_token', $token)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        Auth::login($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
