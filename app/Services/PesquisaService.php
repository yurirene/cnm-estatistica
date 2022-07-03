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
                
            ]);
        } catch (\Throwable $th) {
            throw new Exception("Error Processing Request", 1);
        }
    }

}