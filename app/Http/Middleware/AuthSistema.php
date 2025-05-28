<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
            Log::alert('Acesso negado', [
                'user_id' => auth()->user()->id,
                'user_role' => auth()->user()->role_id,
                'route' => $route,
                'ip' => $request->ip(),
            ]);
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
