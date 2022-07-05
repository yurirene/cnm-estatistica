<?php

namespace App\Services;

use App\Models\Pesquisa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PesquisaService
{
    public static function store(Request $request)
    {
        try {
            Pesquisa::create([
                'nome' => $request->nome,
                'formulario' => $request->formulario
            ]);
        } catch (\Throwable $th) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public static function responder(Request $request)
    {
        dd('fim');
    }

}