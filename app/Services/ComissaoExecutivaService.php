<?php

namespace App\Services;

use App\Models\ComissaoExecutiva\DocumentoRecebido;
use App\Models\ComissaoExecutiva\Reuniao;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ComissaoExecutivaService
{

    public static function store(array $dados): ?Reuniao
    {
        DB::beginTransaction();

        try {

            if (Reuniao::where('status', 1)->get()->isNotEmpty()) {
                throw new Exception("Existe uma reunião em aberto, finalize primeiro para depois criar outra", 500);
            }

            $reuniao = Reuniao::create([
                'ano' => $dados['ano'],
                'local' => $dados['local'],
                'aberto' => isset($dados['aberto']) ? 1 : 0
            ]);

            if (!self::sincronizarSIGCE($reuniao->toArray(), 'nova-reuniao')) {
                throw new Exception("Erro de comunicação com o SIGCE");
            }

            DB::commit();

            return $reuniao;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public static function update(array $dados, Reuniao $reuniao): ?Reuniao
    {

        DB::beginTransaction();

        try {
            $reuniao->update([
                'ano' => $dados['ano'],
                'local' => $dados['local'],
                'aberto' => isset($dados['aberto']) ? 1 : 0
            ]);


            if (!self::sincronizarSIGCE($reuniao->toArray(), 'atualizar-reuniao')) {
                throw new Exception("Erro de comunicação com o SIGCE");
            }

            DB::commit();

            return $reuniao;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public static function delete(Reuniao $reuniao): void
    {
        if ($reuniao->documentos()->get()->isNotEmpty()) {
            throw new Exception("Existem documentos relacionados nessa reunião", 1);
        }


        DB::beginTransaction();

        try {
            $reuniao->delete();

            if (!self::sincronizarSIGCE($reuniao->toArray(), 'deletar-reuniao')) {
                throw new Exception("Erro de comunicação com o SIGCE");
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function encerrar(Reuniao $reuniao): void
    {
        DB::beginTransaction();

        try {
            $reuniao->update([
                'status' => false
            ]);

            if (!self::sincronizarSIGCE($reuniao->toArray(), 'atualizar-reuniao')) {
                throw new Exception("Erro de comunicação com o SIGCE");
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function getReuniaoAberta(): array
    {
        $reuniao = Reuniao::where('status', 1)->where('aberto', 1)->first();

        if (empty($reuniao)) {
            return [];
        }

        return $reuniao->toArray();
    }

    public static function salvarDocumento(array $dados): ?DocumentoRecebido
    {
        $reuniao = self::getReuniaoAberta();

        if (empty($reuniao)) {
            throw new Exception("Nenhuma reunião está aberta para envio de documentos");
        }

        $documento = DocumentoRecebido::create([
            'titulo' => $dados['titulo'],
            'path' => $dados['arquivo'],
            'sinodal_id' => UserService::getInstanciaUsuarioLogado()->id,
            'reuniao_id' => $reuniao['id'],
            'tipo' => $dados['tipo']
        ]);

        return $documento;
    }

    public static function removerDocumento(string $documetoId): void
    {
        $documento = DocumentoRecebido::where('id', $documetoId)->first();

        if (empty($documento)) {
            throw new Exception("Documento não encontrado");
        }

        if ($documento->status == DocumentoRecebido::STATUS_DOCUMENTO_RECEBIDO) {
            throw new Exception("Este documento já foi recebido pela CNM");
        }

        Storage::delete($documento->getRawOriginal('path'));
        $documento->delete();
    }

    public static function getTiposDocumentos(): array
    {
        return DocumentoRecebido::TIPOS_DOCUMENTOS;
    }

    public static function sincronizarSIGCE($reuniao, $endpoint): bool
    {
        $baseURL = config('app.url_integracao_sigce');
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $dados = [
            'reuniao' => $reuniao,
            'chave' => config('app.chave_integracao_sigce')
        ];

        $client = new Client($headers);
        $request = $client->request(
            'POST',
            "{$baseURL}/reuniao/{$endpoint}",
            [
                'form_params' => $dados
            ]
        );

        $responseCode = $request->getStatusCode();

        return $responseCode == 200;
    }

    public static function confirmarDocumento(string $documetoId): void
    {
        $documento = DocumentoRecebido::where('id', $documetoId)->first();

        if (empty($documento)) {
            throw new Exception("Documento não encontrado");
        }

        $status = $documento->status;

        $documento->update([
            'status' => !$status
        ]);
    }


}
