<?php

namespace App\Services;

use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
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
                'aberto' => isset($dados['aberto']) ? 1 : 0,
                'visible' => true
            ]);

            /*
             if (!self::sincronizarSIGCE($reuniao->toArray(), 'nova-reuniao')) {
                throw new Exception("Erro de comunicação com o SIGCE");
            }
            */

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


            // if (!self::sincronizarSIGCE($reuniao->toArray(), 'atualizar-reuniao')) {
            //     throw new Exception("Erro de comunicação com o SIGCE");
            // }

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
            'tipo' => DocumentoRecebido::TIPO_DOCUMENTO_SINODAL
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

    public static function getDelegado(): ?DelegadoComissaoExecutiva
    {
        return DelegadoComissaoExecutiva::where('sinodal_id', UserService::getInstanciaUsuarioLogado()->id)
            ->where('reuniao_id', self::getReuniaoAberta()['id'])
            ->where('suplente', 0)
            ->first();
    }

    public static function getDelegadoSuplente(): ?DelegadoComissaoExecutiva
    {
        return DelegadoComissaoExecutiva::where('sinodal_id', UserService::getInstanciaUsuarioLogado()->id)
            ->where('reuniao_id', self::getReuniaoAberta()['id'])
            ->where('suplente', 1)
            ->first();
    }

    public static function storeDelegado(array $dados): void
    {
        $reuniao = self::getReuniaoAberta();

        if (empty($reuniao)) {
            throw new Exception("Nenhuma reunião está aberta para cadastro de delegado");
        }

        $sinodal = UserService::getInstanciaUsuarioLogado();

        if (DelegadoComissaoExecutiva::where('sinodal_id', $sinodal->id)
            ->where('reuniao_id', $reuniao['id'])
            ->where('suplente', $dados['suplente'] ?? 0)
            ->exists()
        ) {
            throw new Exception("Já existe um delegado para esta reunião");
        }

        $delegado = DelegadoComissaoExecutiva::create([
            'nome' => $dados['nome'],
            'cpf' => $dados['cpf'],
            'reuniao_id' => $reuniao['id'],
            'sinodal_id' => $sinodal->id,
            'suplente' => $dados['suplente'] ?? 0,
            'path_credencial' => $dados['credencial'] ?? null,
            'status' => DelegadoComissaoExecutiva::STATUS_EM_ANALISE
        ]);
    }

    public static function updateDelegado(array $dados, DelegadoComissaoExecutiva $delegado): void
    {

        $delegado->update([
            'nome' => $dados['nome'],
            'cpf' => $dados['cpf']
        ]);

        if (!empty($dados['credencial'])) {
            $delegado->update([
                'path_credencial' => $dados['credencial']
            ]);
        }
    }
    public static function updateDelegadoExecutiva(array $dados, DelegadoComissaoExecutiva $delegado): void
    {
        $delegado->update([
            'nome' => $dados['nome'],
            'cpf' => $dados['cpf'],
            'status' => $dados['status'],
            'pago' => $dados['pago'] ?? false,
            'credencial' => $dados['credencial'] ?? false
        ]);
    }

    public static function sincronizarInscritos(Reuniao $reuniao): void
    {
        $url = config('app.evento_url') . '/reuniao/listar-inscritos/' . $reuniao->id;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('app.evento_api_token')
        ];

        $response = Http::withHeaders($headers)->post($url);

        if ($response->failed()) {
            throw new Exception("Erro ao sincronizar inscritos");
        }

        $inscritos = $response->json();

        foreach ($inscritos as $inscrito) {
            $cpf = self::formatarCpf($inscrito['cpf']);
            $delegado = DelegadoComissaoExecutiva::where('cpf', $cpf)
                ->where('reuniao_id', $reuniao->id)
                ->first();

            if (empty($delegado) || !in_array($inscrito['payment_status'], DelegadoComissaoExecutiva::STATUS_PAGAMENTO_CONFIRMADO)) {
                continue;
            }

            $delegado->update([
                'status' => DelegadoComissaoExecutiva::STATUS_CONFIRMADA,
                'pago' => true
            ]);
        }
    }

    /**
     * Formata CPF no padrão 000.000.000-00
     * 
     * @param string $cpf CPF sem formatação (apenas números)
     * @return string CPF formatado
     */
    public static function formatarCpf(string $cpf): string
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return $cpf; // Retorna o CPF original se não tiver 11 dígitos
        }
        
        // Formata no padrão 000.000.000-00
        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }
}
