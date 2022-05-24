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

class AtividadeService
{

    public static function store(Request $request)
    {
        try {
            Atividade::create([
                'titulo' => $request->titulo,
                'observacoes' => $request->observacoes,
                'tipo' => $request->tipo,
                'start' => Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                'status' => $request->status == 'A' ? true : false,
                'user_id' => Auth::id()
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

    public static function update(Atividade $atividade, Request $request)
    {
        try {
            $atividade->update([
                'titulo' => $request->titulo,
                'observacoes' => $request->observacoes,
                'tipo' => $request->tipo,
                'start' => Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                'status' => $request->status == 'A' ? true : false,
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

    public static function confirmar(Atividade $atividade)
    {
        try {
            $atividade->update([
                'status' => true,
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

    public static function delete(Atividade $atividade)
    {
        try {
            $atividade->delete();
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }
}