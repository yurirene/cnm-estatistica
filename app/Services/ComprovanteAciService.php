<?php

namespace App\Services;

use App\Models\ComprovanteACI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ComprovanteAciService
{
    public static function store(Request $request) : ComprovanteACI
    {
        try {
            $comprovante = ComprovanteACI::create([
                'sinodal_id' => Auth::user()->sinodais->first()->id,
                'ano' => date('Y')
            ]);

            if ($request->has('comprovante_aci')) {
                $path = $request->file('comprovante_aci')->store('public/atividades');
                $comprovante->update([
                    'comprovante_aci' => '/' . str_replace('public', 'storage', $path)
                ]);
            }

            return $comprovante;
        } catch (Throwable $th) {
            throw $th;
        }
    }
}