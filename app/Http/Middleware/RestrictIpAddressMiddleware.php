<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIpAddressMiddleware
{

    // Blocked IP addresses
    public $restrictedIp = ['185.81.68.180', 
'104.36.23.10', '149.154.161.237', '63.250.33.242'];
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
            abort(406, 'Seu IP está bloqueado');
        }
        return $next($request);
    }
}
