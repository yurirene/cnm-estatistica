<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenSicomMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        $expectedToken = env('SICOM_API_TOKEN');

        if (!$authHeader || !preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $matches[1];

        if ($token !== $expectedToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
