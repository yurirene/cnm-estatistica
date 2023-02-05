<?php

namespace App\Services;

use App\Models\RegistroLogin;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class LoginService
{
    public static function login(Request $request, User $usuario)
    {
        try {
            RegistroLogin::create([
                'user_id' => $usuario->id,
                'email' => $usuario->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => Carbon::now(),
            ]);
        } catch (\Throwable $th) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public static function logout($usuario)
    {
        try {
            $registro = RegistroLogin::where('user_id', $usuario->id)->orderBy('created_at', 'desc')->first();
            if (!$registro) {
                return;
            }
            $registro->update([
                'logout_at' => Carbon::now()
            ]);
        } catch (\Throwable $th) {
            throw new Exception("Error Processing Request", 1);
        }
    }

}
