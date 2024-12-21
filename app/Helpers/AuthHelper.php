<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthHelper
{

    public static function check($route)
    {
        if (Gate::allows('rota-permitida', [$route])) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

}
