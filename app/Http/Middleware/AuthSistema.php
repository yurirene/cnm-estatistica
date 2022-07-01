<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthSistema
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->getName();
        if ($route == 'dashboard.home') {
            return $next($request);
        }
        if (!auth()->user()->canAtLeast([$route])) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Voce não tem permissão!'
                ]
            ]);
        }

        return $next($request);
    }
}
