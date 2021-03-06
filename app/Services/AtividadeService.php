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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AtividadeService
{

    public static function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $atividade = Atividade::create([
                'titulo' => $request->titulo,
                'observacoes' => $request->observacoes,
                'tipo' => $request->tipo,
                'start' => Carbon::createFromFormat('d/m/Y', $request->start)->format('Y-m-d'),
                'status' => $request->status == 'A' ? true : false,
                'user_id' => Auth::id()
            ]);
            if ($request->has('imagem')) {
                $path = $request->file('imagem')->store('public/atividades');
                $atividade->update([
                    'imagem' => '/' . str_replace('public', 'storage', $path)
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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
            if ($request->has('imagem')) {
                $path = $request->file('imagem')->store('public/atividades');
                $atividade->update([
                    'imagem' => '/' . str_replace('public', 'storage', $path)
                ]);
            }
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
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
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Atualizar");
            
        }
    }

    public static function delete(Atividade $atividade)
    {
        try {
            $atividade->delete();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
            throw new Exception("Erro ao Atualizar");
            
        }
    }
}