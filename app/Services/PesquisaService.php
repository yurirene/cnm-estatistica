<?php

namespace App\Services;

use App\Models\Pesquisa;
use App\Models\PesquisaResposta;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PesquisaService
{
    public static function store(Request $request)
    {
        try {
            $referencias = self::referenciaCamposFormulario($request->formulario);
            Pesquisa::create([
                'nome' => $request->nome,
                'formulario' => $request->formulario,
                'referencias' => $referencias,
                'user_id' => Auth::id()
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'mensagem' => $th->getMessage(),
                'linha' => $th->getLine(),
                'arquivo' => $th->getFile()
            ]);
            throw new Exception("Erro ao salvar o formulário", 1);
        }
    }

    public static function referenciaCamposFormulario(string $formulario) : array
    {
        try {
            $json_formulario = json_decode($formulario, true);
            $array_formulario = json_decode($json_formulario, true);
            $referencias = array();
            foreach ($array_formulario as $key => $campo) {
                if (in_array($campo['type'], ['button', 'paragraph', 'header'])) {
                    continue;
                }
                $referencias[$key] = [
                    $campo['name'] => [
                        'campo' => isset($campo['label']) ? Str::snake($campo['label']) : '',
                        'required' => $campo['required'] ?? false,
                    ]
                ]; 
                if (!isset($campo['values'])) {
                    continue;
                }
                foreach ($campo['values'] as $opcao) {
                    $referencias[$key][$campo['name']]['valores'][] = $opcao['value'];       
                }
            }
            return $referencias;
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
                'line' => $th->getLine(), 
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function responder(Request $request)
    {
        try {
            $pesquisa = Pesquisa::findOrFail($request->pesquisa_id);
            $resposta = [];
            $referencias = $pesquisa->referencias;
            foreach ($referencias as $referencia) {
                foreach ($referencia as $campo => $valor) {
                    $resposta[$valor['campo']] = $request[$campo] ?? null;
                }
            }

            PesquisaResposta::updateOrCreate(
                [
                    'pesquisa_id' => $pesquisa->id,
                    'user_id' => Auth::id()
                ],
                [
                    'resposta' => $resposta,
                ]
            );
            return true;
        } catch (Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
                'line' => $th->getLine(), 
                'file' => $th->getFile()
            ]);
            throw new Exception('Erro ao Responder');
        }
    }

    public static function status(Pesquisa $pesquisa)
    {
        try {
            $pesquisa->update([
                'status' => !$pesquisa->status
            ]);
        } catch (Throwable $th) {
            Log::error([
                'message' => $th->getMessage(),
                'line' => $th->getLine(), 
                'file' => $th->getFile()
            ]);
            throw new Exception('Erro ao Responder');
        }
    }

    public static function getDadosRepostas(Pesquisa $pesquisa)
    {
        $respostas = $pesquisa->respostas;
        dd($respostas);
    }

}