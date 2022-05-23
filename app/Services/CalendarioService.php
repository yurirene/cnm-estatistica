<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Atividade;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CalendarioService
{

    public static function store(Request $request)
    {
        try {
            $federacao = Federacao::find($request->federacao_id);
            Atividade::create([
                'nome' => $request->nome,
                'estado_id' => $federacao->estado_id,
                'federacao_id' => $federacao->id,
                'sinodal_id' => $federacao->sinodal_id,
                'regiao_id' => $federacao->regiao_id,
                'status' => $request->status == 'A' ? true : false
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");
            
        }
    }

    public static function update(Atividade $sinodal, Request $request)
    {
        try {
            $regiao = Estado::find($request->estado_id)->regiao_id;
            $sinodal->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
                'estado_id' => $request->estado_id,
                'federacao_id' => $request->federacao_id,
                'sinodal_id' => $request->sinodal_id,
                'regiao_id' => $regiao,
                'status' => $request->status == 'A' ? true : false
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }
    
    public static function getCalendario($request)
    {
        try {
            $usuario = User::find(Auth::id());
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->start)->addMonth();
            $atividades = $usuario->atividades()
                ->where('start', '>', $start->format('Y-m-d'))
                ->where('start', '<', $end->format('Y-m-d'))
                ->get()
                ->map(function($evento) {
                    return [
                        'id' => $evento->id,
                        'title' => $evento->titulo,
                        'start' => $evento->start->format('Y-m-d'),
                        'end' => $evento->start->format('Y-m-d'),
                    ];
                });
            return $atividades;
        } catch (Throwable $th) {
            return $th->getMessage();
        }
    }

}