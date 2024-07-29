<?php

namespace App\Services;

use App\Models\ComissaoExecutiva\DocumentoRecebido;
use App\Models\ComissaoExecutiva\Reuniao;
use Exception;
use Illuminate\Support\Facades\Storage;

class ComissaoExecutivaService
{

    public static function store(array $dados): ?Reuniao
    {
        if (Reuniao::where('status', 1)->get()->isNotEmpty()) {
            throw new Exception("Existe uma reunião em aberto, finalize primeiro para depois criar outra", 500);
        }

        return Reuniao::create([
            'ano' => $dados['ano'],
            'local' => $dados['local'],
            'aberto' => isset($dados['aberto']) ? 1 : 0
        ]);
    }


    public static function update(array $dados, Reuniao $reuniao): ?Reuniao
    {
        $reuniao->update([
            'ano' => $dados['ano'],
            'local' => $dados['local'],
            'aberto' => isset($dados['aberto']) ? 1 : 0
        ]);

        return $reuniao;
    }


    public static function delete(Reuniao $reuniao): void
    {
        if ($reuniao->documentos()->get()->isNotEmpty()) {
            throw new Exception("Existem documentos relacionados nessa reunião", 1);
        }

        $reuniao->delete();
    }

    public static function encerrar(Reuniao $reuniao): void
    {
        $reuniao->update([
            'status' => false
        ]);
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
        // self::informarNovoDocumentoSIGCE($documento->toArray());

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

}
