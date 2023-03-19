<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIpAddressMiddleware
{

    // Blocked IP addresses
    public $restrictedIp = ['185.81.68.180'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array($request->ip(), $this->restrictedIp)) {
            abort( response()->json('Seu IP está bloqueado', 401) );
        }
        return $next($request);
    }
}
