<?php

namespace App\Services;

use App\Models\ColetorDados;
use App\Services\Estatistica\EstatisticaService;
use Exception;
use Illuminate\Support\Facades\DB;

class ColetorDadosService
{
    /**
     * Cria os formulários de coleta de dados
     * com base na quantidade informada
     * 
     * @param array $request
     * 
     * @return void
     */
    public static function store(array $request)
    {
        DB::beginTransaction();
        try {
            $localId = auth()->user()->locais->first()->id;

            if ((int) $request['quantidade'] < 0) {
                throw new Exception("Quantidade deve ser maior que 0");
            }

            for ($i = 0; $i < $request['quantidade']; $i++) {
                ColetorDados::create([
                    'local_id' => $localId,
                    'ano' => EstatisticaService::getAnoReferencia(),
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

    /**
     * Salva a resposta do formulário
     * 
     * @param string $id
     * @param array $request
     * 
     * @return void
     */
    public static function responder(string $id, array $request)
    {
        DB::beginTransaction();
        try {
            $formulario = ColetorDados::findOrFail($id);
            $formulario->update([
                'resposta' => $request
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Responder");
        }
    }

    /**
     * Remove um formulário se não estiver preenchido
     * 
     * @param string $id
     * 
     * @return void
     */
    public static function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $formulario = ColetorDados::findOrFail($id);
            
            if (!$formulario->status && !empty($formulario->resposta)) {
                throw new Exception("Formulário já respondido não pode ser removido", 1);
            }

            $formulario->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao Remover");
        }
    }

    /**
     * Retorna informações do formulário e da ump que pertence
     * 
     * @param string $id
     * 
     * @return array
     */
    public static function carregar(string $id): array
    {
        $formulario = ColetorDados::findOrFail($id);
        $local = $formulario->local;

        return [
            'formulario' => $formulario,
            'local' => $local
        ];
    }
}
