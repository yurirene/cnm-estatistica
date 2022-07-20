<?php

namespace App\Services;

use App\Models\Pesquisa;
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

    public static function update(Pesquisa $pesquisa, Request $request)
    {
        try {
            $referencias = self::referenciaCamposFormulario($request->formulario);
            $pesquisa->update([
                'nome' => $request->nome,
                'formulario' => $request->formulario,
                'referencias' => $referencias,
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

    public static function referenciaCamposFormulario(string $formulario)
    {
        $json_formulario = json_decode($formulario, true);
        $array_formulario = json_decode($json_formulario, true);
        $referencias = array();
        foreach ($array_formulario as $key => $campo) {
            if (in_array($campo['type'], ['button', 'paragraph'])) {
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
    }

    public static function responder(Request $request)
    {
        try {
            $pesquisa = Pesquisa::findOrFail($request->pesquisa_id);
            
            dd($request->all());

        } catch (Throwable $th) {
            throw new Exception('Erro ao Responder');
        }
    }

}