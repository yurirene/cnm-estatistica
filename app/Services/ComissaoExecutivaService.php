<?php

namespace App\Services;

use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\ComissaoExecutiva\DocumentoRecebido;
use App\Models\ComissaoExecutiva\DocumentosAutomaticos;
use App\Models\ComissaoExecutiva\Reuniao;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Formularios\FormularioSinodalService;
use App\Services\Gamificacao\GamificacaoHook;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ComissaoExecutivaService
{

    public const DIACONO = 1;
    public const PRESBITERO = 2;

    public const TIPOS_OFICIAIS = [
        self::DIACONO => 'Diácono',
        self::PRESBITERO => 'Presbítero'
    ];

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
                'visible' => true,
                'diretoria' => isset($dados['diretoria']) ? 1 : 0,
                'relatorio_estatistico' => isset($dados['relatorio_estatistico']) ? 1 : 0
            ]);

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
                'aberto' => isset($dados['aberto']) ? 1 : 0,
                'diretoria' => isset($dados['diretoria']) ? 1 : 0,
                'relatorio_estatistico' => isset($dados['relatorio_estatistico']) ? 1 : 0
            ]);

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

            DB::commit();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
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
            'oficial' => $dados['oficial'] ?? null,
            'telefone' => $dados['telefone'] ?? null,
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
            'cpf' => $dados['cpf'],
            'telefone' => $dados['telefone'] ?? null,
            'oficial' => $dados['oficial'] ?? null
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
            'telefone' => $dados['telefone'],
            'oficial' => $dados['oficial'],
            'status' => $dados['status'],
            'pago' => $dados['pago'] ?? false,
            'credencial' => $dados['credencial'] ?? false
        ]);

        if (! empty($delegado->sinodal_id)) {
            GamificacaoHook::aposComissaoExecutiva($delegado->sinodal_id);
        }
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
                'status' => $delegado->credencial ? DelegadoComissaoExecutiva::STATUS_CONFIRMADA : DelegadoComissaoExecutiva::STATUS_EM_ANALISE,
                'telefone' => $inscrito['phone'],
                'pago' => true
            ]);

            if (! empty($delegado->sinodal_id)) {
                GamificacaoHook::aposComissaoExecutiva($delegado->sinodal_id);
            }
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

    public static function getDocumentosAutomaticosEntregues(string $reuniao): array
    {
        $reuniao = Reuniao::where('id', $reuniao)->first();
        $instancia = UserService::getCampoInstanciaDB();

        if (empty($reuniao)) {
            return [];
        }

        $documentos = [];

        $documentosAutomaticos = DocumentosAutomaticos::where('reuniao_id', $reuniao->id)
            ->where($instancia['campo'], $instancia['id'])
            ->first();

        if (!empty($documentosAutomaticos)) {
            $documentosAutomaticos = $documentosAutomaticos->toArray();
        } else {
            $documentosAutomaticos = [];
        }

        if ($reuniao->diretoria == 1) {
            $documentos['diretoria'] = isset($documentosAutomaticos['diretoria']) ? true : false;
        }

        if ($reuniao->relatorio_estatistico == 1) {
            $documentos['relatorio_estatistico'] = isset($documentosAutomaticos['relatorio_estatistico']) ? true : false;
        }

        return $documentos;
    }

    public static function deveNotificarDiretoria(): bool
    {
        $reuniao = Reuniao::where('status', 1)->where('aberto', 1)->first();

        if (!$reuniao) {
            return false;
        }

        return (bool) $reuniao->diretoria;
    }

    public static function deveNotificarRelatorioEstatistico(): bool
    {
        $reuniao = Reuniao::where('status', 1)
            ->where('aberto', 1)
            ->first();

        if (empty($reuniao)) {
            return false;
        }

        return (bool) $reuniao->relatorio_estatistico;
    }

    /**
     * Gera ZIP com os documentos da reunião (exceto credenciais).
     *
     * @return array{path: string, downloadName: string}
     */
    public static function gerarZipDocumentos(Reuniao $reuniao): array
    {
        $documentos = self::aplicarFiltroPerfil(
            DocumentoRecebido::with(['sinodal.regiao'])
                ->where('reuniao_id', $reuniao->id)
                ->where('tipo', '!=', DocumentoRecebido::TIPO_CREDENCIAL_SINODAL)
        )->get();

        $entradas = [];
        $nomesUsados = [];

        foreach ($documentos as $documento) {
            $rawPath = $documento->getRawOriginal('path');
            if (!$rawPath || !Storage::exists($rawPath)) {
                continue;
            }

            $regiaoNome = $documento->sinodal?->regiao?->nome ?? 'sem_regiao';
            $siglaUnidade = $documento->sinodal?->sigla ?? 'sem_sigla';
            $titulo = $documento->titulo ?? 'sem_titulo';
            $ext = pathinfo($rawPath, PATHINFO_EXTENSION);
            $base = 'doc_' . self::slugParaArquivo($regiaoNome)
                . '_' . self::slugParaArquivo($siglaUnidade)
                . '_' . self::slugParaArquivo($titulo);
            $nomeArquivo = self::nomeUnicoNoZip($base . ($ext !== '' ? '.' . $ext : ''), $nomesUsados);

            $entradas[] = [
                'path' => Storage::path($rawPath),
                'nome' => $nomeArquivo,
            ];
        }

        return self::gerarZipReuniao(
            $reuniao,
            $entradas,
            'documentos-ce',
            'Nenhum documento encontrado para esta reunião.'
        );
    }

    /**
     * Gera ZIP com as credenciais da reunião (delegados e documentos do tipo credencial).
     *
     * @return array{path: string, downloadName: string}
     */
    public static function gerarZipCredenciais(Reuniao $reuniao): array
    {
        $delegados = self::aplicarFiltroPerfil(
            DelegadoComissaoExecutiva::with(['sinodal.regiao'])
                ->where('reuniao_id', $reuniao->id)
                ->whereNotNull('path_credencial')
        )->get();

        $documentosCredencial = self::aplicarFiltroPerfil(
            DocumentoRecebido::with(['sinodal.regiao'])
                ->where('reuniao_id', $reuniao->id)
                ->where('tipo', DocumentoRecebido::TIPO_CREDENCIAL_SINODAL)
        )->get();

        $entradas = [];
        $nomesUsados = [];

        foreach ($delegados as $delegado) {
            $rawPath = $delegado->getRawOriginal('path_credencial');
            if (!$rawPath || !Storage::exists($rawPath)) {
                continue;
            }

            $regiaoNome = $delegado->sinodal?->regiao?->nome ?? 'sem_regiao';
            $siglaUnidade = $delegado->sinodal?->sigla ?? 'sem_sigla';
            $nomeDelegado = $delegado->nome ?? 'sem_nome';
            $ext = pathinfo($rawPath, PATHINFO_EXTENSION);
            $sufixo = $delegado->suplente ? 'suplente' : 'delegado';
            $base = 'credencial_' . self::slugParaArquivo($regiaoNome)
                . '_' . self::slugParaArquivo($siglaUnidade)
                . '_' . self::slugParaArquivo($nomeDelegado)
                . '_' . $sufixo;
            $nomeArquivo = self::nomeUnicoNoZip($base . ($ext !== '' ? '.' . $ext : ''), $nomesUsados);

            $entradas[] = [
                'path' => Storage::path($rawPath),
                'nome' => $nomeArquivo,
            ];
        }

        foreach ($documentosCredencial as $documento) {
            $rawPath = $documento->getRawOriginal('path');
            if (!$rawPath || !Storage::exists($rawPath)) {
                continue;
            }

            $regiaoNome = $documento->sinodal?->regiao?->nome ?? 'sem_regiao';
            $siglaUnidade = $documento->sinodal?->sigla ?? 'sem_sigla';
            $titulo = $documento->titulo ?? 'sem_titulo';
            $ext = pathinfo($rawPath, PATHINFO_EXTENSION);
            $base = 'credencial_' . self::slugParaArquivo($regiaoNome)
                . '_' . self::slugParaArquivo($siglaUnidade)
                . '_' . self::slugParaArquivo($titulo);
            $nomeArquivo = self::nomeUnicoNoZip($base . ($ext !== '' ? '.' . $ext : ''), $nomesUsados);

            $entradas[] = [
                'path' => Storage::path($rawPath),
                'nome' => $nomeArquivo,
            ];
        }

        return self::gerarZipReuniao(
            $reuniao,
            $entradas,
            'credenciais-ce',
            'Nenhuma credencial encontrada para esta reunião.'
        );
    }

    /**
     * Converte texto para uso em nome de arquivo: sem acentuação, snake_case.
     */
    public static function slugParaArquivo(?string $texto): string
    {
        if ($texto === null || trim($texto) === '') {
            return 'sem_nome';
        }

        return Str::slug($texto, '_');
    }

    /**
     * Garante nome único dentro do ZIP (evita sobrescrever).
     */
    public static function nomeUnicoNoZip(string $nomeArquivo, array &$nomesUsados): string
    {
        $nome = $nomeArquivo;
        $cont = 0;
        while (isset($nomesUsados[$nome])) {
            $cont++;
            $info = pathinfo($nomeArquivo);
            $nome = ($info['filename'] ?? $nomeArquivo)
                . '_' . $cont
                . (isset($info['extension']) ? '.' . $info['extension'] : '');
        }
        $nomesUsados[$nome] = true;

        return $nome;
    }

    /**
     * Monta o arquivo ZIP a partir das entradas e retorna a quantidade incluída.
     *
     * @param array<int, array{path: string, nome: string}> $entradas
     */
    public static function montarZip(string $zipPath, array $entradas): int
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Não foi possível criar o arquivo ZIP.');
        }

        $totalArquivos = 0;
        foreach ($entradas as $entrada) {
            if (empty($entrada['path']) || empty($entrada['nome']) || !is_file($entrada['path'])) {
                continue;
            }
            $zip->addFile($entrada['path'], $entrada['nome']);
            $zip->setCompressionName($entrada['nome'], ZipArchive::CM_DEFLATE, 9);
            $totalArquivos++;
        }

        $zip->close();

        return $totalArquivos;
    }

    /**
     * @param array<int, array{path: string, nome: string}> $entradas
     * @return array{path: string, downloadName: string}
     */
    private static function gerarZipReuniao(
        Reuniao $reuniao,
        array $entradas,
        string $prefixoArquivo,
        string $mensagemVazio
    ): array {
        $dir = storage_path('app/temp');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zipPath = $dir . '/' . $prefixoArquivo . '-' . $reuniao->id . '.zip';
        $totalArquivos = self::montarZip($zipPath, $entradas);

        if ($totalArquivos === 0) {
            @unlink($zipPath);
            throw new Exception($mensagemVazio);
        }

        $nomeDownload = $prefixoArquivo
            . '-' . self::slugParaArquivo($reuniao->local)
            . '-' . $reuniao->ano
            . '.zip';

        return [
            'path' => $zipPath,
            'downloadName' => $nomeDownload,
        ];
    }

    private static function aplicarFiltroPerfil(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user || !$user->role) {
            return $query;
        }

        if ($user->role->name === User::ROLE_DIRETORIA) {
            $sinodais = Sinodal::where('regiao_id', $user->regiao_id)->pluck('id');

            return $query->whereIn('sinodal_id', $sinodais);
        }

        if (!in_array($user->role->name, [User::ROLE_SEC_EXECUTIVA, User::ROLE_DIRETORIA], true)) {
            return $query->where('sinodal_id', $user->sinodal_id);
        }

        return $query;
    }

    public static function notificarRelatorioEstatistico(): void
    {
        $reuniao = Reuniao::where('status', 1)
            ->where('aberto', 1)
            ->first();

        if (empty($reuniao)) {
            throw new Exception("Nenhuma reunião está aberta para envio de notificação");
        }

        $instancia = UserService::getCampoInstanciaDB();

        DB::beginTransaction();

        try {
            $formulario = FormularioSinodalService::getFormularioAnoCorrente();

            if (empty($formulario)) {
                throw new Exception("Nenhum formulário de relatório estatístico encontrado");
            }

            DocumentosAutomaticos::updateOrCreate(
                [
                    $instancia['campo'] => $instancia['id'],
                    'reuniao_id' => $reuniao->id
                ],
                [
                    'reuniao_id' => $reuniao->id,
                    $instancia['campo'] => $instancia['id'],
                    'relatorio_estatistico' => $formulario->toArray()
                ]
            );

            $formulario->update([
                'reuniao_notificada' => $reuniao->id
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw new Exception("Erro ao notificar relatório estatístico");
        }
    }
}
