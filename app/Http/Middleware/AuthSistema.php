<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthSistema
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->getName();
        $excpetions = [
            'dashboard.home',
            'dashboard.trocar-senha',
            'dashboard.usuarios.check-usuario'
        ];
        if (in_array($route, $excpetions)) {
            return $next($request);
        }
        if (Gate::denies('rota-permitida', [$route])) {
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
