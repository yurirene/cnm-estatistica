<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{

    public static function check($route)
    {
       
        if (!auth()->user()->canAtLeast([$route])) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

}