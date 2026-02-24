<?php

namespace App\Http\Controllers\Congresso;

use App\Http\Controllers\Controller;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\CongressoNacional\DelegadoCongressoNacional;
use App\Models\CongressoNacional\DocumentoRecebido;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Rules\Cpf;
use App\Services\AvisoService;
use App\Services\DatatableAjaxService;
use App\Services\LogErroService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CongressoNacionalController extends Controller
{
    public function indexSinodal()
    {
        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();
            $delegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)
                ->whereNull('federacao_id')
                ->get();

            $totalDelegados = $delegados->count();
            $limiteAtingido = $totalDelegados >= 1;

            $documentos = DocumentoRecebido::where('sinodal_id', $sinodal->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dashboard.congresso-nacional.sinodal.index', [
                'delegados' => $delegados,
                'documentos' => $documentos,
                'totalDelegados' => $totalDelegados,
                'limiteAtingido' => $limiteAtingido
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function createSinodal()
    {
        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();

            // Verificar limite de 1 delegado por sinodal
            $totalDelegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)
                ->whereNull('federacao_id')
                ->count();
            if ($totalDelegados >= 1) {
                return redirect()->route('dashboard.cn.sinodal.index')->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'O limite máximo de 1 delegado por sinodal foi atingido!'
                    ]
                ]);
            }

            $delegado = new DelegadoCongressoNacional();
            return view('dashboard.congresso-nacional.sinodal.delegado', [
                'delegado' => $delegado
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function editSinodal(string $delegado)
    {
        try {
            $delegado = DelegadoCongressoNacional::findOrFail($delegado);
            return view('dashboard.congresso-nacional.sinodal.delegado', [
                'delegado' => $delegado
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.congresso-nacional.sinodal.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function indexFederacao()
    {
        try {
            $federacao = UserService::getInstanciaUsuarioLogado();
            $delegados = DelegadoCongressoNacional::where('federacao_id', $federacao->id)
                ->get();

            $totalDelegados = $delegados->count();
            $limiteAtingido = $totalDelegados >= 6;

            // Documentos da sinodal relacionada à federação
            $documentos = DocumentoRecebido::where('sinodal_id', $federacao->sinodal_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dashboard.congresso-nacional.federacao.index', [
                'delegados' => $delegados,
                'documentos' => $documentos,
                'totalDelegados' => $totalDelegados,
                'limiteAtingido' => $limiteAtingido
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function createFederacao()
    {
        try {
            $federacao = UserService::getInstanciaUsuarioLogado();

            // Verificar limite de 6 delegados por federação
            $totalDelegados = DelegadoCongressoNacional::where('federacao_id', $federacao->id)->count();
            if ($totalDelegados >= 6) {
                return redirect()->route('dashboard.cn.federacao.index')->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'O limite máximo de 6 delegados por federação foi atingido!'
                    ]
                ]);
            }

            $delegado = new DelegadoCongressoNacional();
            return view('dashboard.congresso-nacional.federacao.delegado', [
                'delegado' => $delegado
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.cn.federacao.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function editFederacao(string $delegado)
    {
        try {
            $delegado = DelegadoCongressoNacional::findOrFail($delegado);
            return view('dashboard.congresso-nacional.federacao.delegado', [
                'delegado' => $delegado
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.cn.federacao.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function storeDelegadoFederacao(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'cpf' => ['required', 'string', new Cpf()],
            'oficial' => 'required|in:0,1,2',
            'credencial_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'comissoes' => 'nullable|array|max:2',
            'comissoes.*' => 'in:relatorios_gestao,planejamento_estrategico,gtsi,atas',
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'comissoes.max' => 'Você pode selecionar no máximo 2 comissões',
            'comissoes.*.in' => 'Uma ou mais comissões selecionadas são inválidas',
            'credencial_file.required' => 'A credencial é obrigatória',
        ]);

        try {
            $federacao = UserService::getInstanciaUsuarioLogado();

            // Verificar limite de 6 delegados por federação
            $totalDelegados = DelegadoCongressoNacional::where('federacao_id', $federacao->id)->count();
            if ($totalDelegados >= 6) {
                return redirect()->back()->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'O limite máximo de 6 delegados por federação foi atingido!'
                    ]
                ])->withInput();
            }

            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'oficial' => $request->oficial,
                'pago' => $request->has('pago') ? 1 : 0,
                'credencial' => $request->has('credencial') ? 1 : 0,
                'federacao_id' => $federacao->id,
                'sinodal_id' => $federacao->sinodal_id,
                'status' => DelegadoCongressoNacional::STATUS_EM_ANALISE
            ];

            if ($request->has('comissoes') && is_array($request->comissoes)) {
                $dados['comissoes'] = $request->comissoes;
            }

            if ($request->hasFile('credencial_file')) {
                $dados['path_credencial'] = $request->file('credencial_file');
            }

            DelegadoCongressoNacional::create($dados);

            return redirect()->route('dashboard.cn.federacao.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado cadastrado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Algo deu Errado!'
                ]
            ])->withInput();
        }
    }

    public function updateDelegadoFederacao(Request $request, DelegadoCongressoNacional $delegado): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'cpf' => ['required', 'string', new Cpf()],
            'oficial' => 'required|in:0,1,2',
            'credencial_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'comissoes' => 'nullable|array|max:2',
            'comissoes.*' => 'in:relatorios_gestao,planejamento_estrategico,gtsi,atas',
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'comissoes.max' => 'Você pode selecionar no máximo 2 comissões',
            'comissoes.*.in' => 'Uma ou mais comissões selecionadas são inválidas',
        ]);

        try {
            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'oficial' => $request->oficial,
            ];

            if ($request->has('comissoes') && is_array($request->comissoes)) {
                $dados['comissoes'] = $request->comissoes;
            } elseif ($request->has('comissoes') && empty($request->comissoes)) {
                $dados['comissoes'] = null;
            }

            if ($request->hasFile('credencial_file')) {
                $dados['path_credencial'] = $request->file('credencial_file');
            }

            $delegado->update($dados);

            return redirect()->route('dashboard.cn.federacao.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado atualizado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Algo deu Errado!'
                ]
            ])->withInput();
        }
    }

    public function deleteFederacao(string $delegado): RedirectResponse
    {
        try {
            $delegado = DelegadoCongressoNacional::findOrFail($delegado);
            $delegado->delete();

            return redirect()->route('dashboard.cn.federacao.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado excluído com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Erro ao excluir delegado!'
                ]
            ]);
        }
    }

    public function storeDelegadoSinodal(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'oficial' => 'required|in:0,1,2',
            'credencial_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'cpf' => ['required', 'string', new Cpf()],
            'comissoes' => 'nullable|array|max:2',
            'comissoes.*' => 'in:relatorios_gestao,planejamento_estrategico,gtsi,atas',
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'cpf.required' => 'O CPF é obrigatório',
            'comissoes.max' => 'Você pode selecionar no máximo 2 comissões',
            'comissoes.*.in' => 'Uma ou mais comissões selecionadas são inválidas',
            'credencial_file.required' => 'A credencial é obrigatória',
        ]);

        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();

            // Verificar limite de 1 delegado por sinodal
            $totalDelegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)
                ->whereNull('federacao_id')
                ->count();
            if ($totalDelegados >= 1) {
                return redirect()->back()->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => 'O limite máximo de 1 delegado por sinodal foi atingido!'
                    ]
                ])->withInput();
            }

            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'oficial' => $request->oficial,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'pago' => $request->has('pago') ? 1 : 0,
                'credencial' => $request->has('credencial') ? 1 : 0,
                'sinodal_id' => $sinodal->id,
                'status' => DelegadoCongressoNacional::STATUS_EM_ANALISE
            ];

            if ($request->has('comissoes') && is_array($request->comissoes)) {
                $dados['comissoes'] = $request->comissoes;
            }

            if ($request->hasFile('credencial_file')) {
                $dados['path_credencial'] = $request->file('credencial_file');
            }

            DelegadoCongressoNacional::create($dados);

            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado cadastrado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Algo deu Errado!'
                ]
            ])->withInput();
        }
    }

    public function updateDelegadoSinodal(Request $request, DelegadoCongressoNacional $delegado): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'oficial' => 'required|in:0,1,2',
            'credencial_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'cpf' => ['required', 'string', new Cpf()],
            'comissoes' => 'nullable|array|max:2',
            'comissoes.*' => 'in:relatorios_gestao,planejamento_estrategico,gtsi,atas',
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'cpf.required' => 'O CPF é obrigatório',
            'comissoes.max' => 'Você pode selecionar no máximo 2 comissões',
            'comissoes.*.in' => 'Uma ou mais comissões selecionadas são inválidas',
        ]);

        try {
            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'oficial' => $request->oficial,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            ];

            if ($request->has('comissoes') && is_array($request->comissoes)) {
                $dados['comissoes'] = $request->comissoes;
            } elseif ($request->has('comissoes') && empty($request->comissoes)) {
                $dados['comissoes'] = null;
            }

            if ($request->hasFile('credencial_file')) {
                $dados['path_credencial'] = $request->file('credencial_file');
            }

            $delegado->update($dados);

            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado atualizado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Algo deu Errado!'
                ]
            ])->withInput();
        }
    }

    public function deleteSinodal(string $delegado): RedirectResponse
    {
        try {
            $delegado = DelegadoCongressoNacional::findOrFail($delegado);
            $delegado->delete();

            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado excluído com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Erro ao excluir delegado!'
                ]
            ]);
        }
    }

    public function storeDocumentoSinodal(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'arquivo' => 'required|file|mimes:pdf|max:1024',
        ], [
            'titulo.required' => 'O título do documento é obrigatório',
            'arquivo.required' => 'O arquivo é obrigatório',
            'arquivo.mimes' => 'O arquivo deve ser um PDF',
            'arquivo.max' => 'O arquivo deve ter no máximo 1MB',
        ]);

        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();

            $documento = DocumentoRecebido::create([
                'titulo' => $request->titulo,
                'path' => $request->file('arquivo'),
                'sinodal_id' => $sinodal->id,
                'status' => DocumentoRecebido::STATUS_DOCUMENTO_PENDENTE
            ]);

            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Documento enviado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao enviar documento!'
                ]
            ])->withInput();
        }
    }

    public function deleteDocumentoSinodal(string $documento): RedirectResponse
    {
        try {
            $documento = DocumentoRecebido::findOrFail($documento);
            $sinodal = UserService::getInstanciaUsuarioLogado();

            // Verificar se o documento pertence à sinodal logada
            if ($documento->sinodal_id !== $sinodal->id) {
                throw new \Exception('Você não tem permissão para excluir este documento');
            }

            // Verificar se o documento já foi recebido
            if ($documento->status == DocumentoRecebido::STATUS_DOCUMENTO_RECEBIDO) {
                throw new \Exception('Este documento já foi recebido e não pode ser excluído');
            }

            // Excluir arquivo físico
            if ($documento->path) {
                Storage::delete($documento->getRawOriginal('path'));
            }

            $documento->delete();

            return redirect()->route('dashboard.cn.sinodal.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Documento excluído com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao excluir documento!'
                ]
            ]);
        }
    }

    /**
     * Lista todos os delegados para o secretário executivo
     */
    public function indexExecutiva()
    {
        try {
            $delegadosFederacao = DelegadoCongressoNacional::with(['federacao', 'sinodal'])
                ->whereNotNull('federacao_id')
                ->orderBy('created_at', 'desc')
                ->get();

            $delegadosSinodal = DelegadoCongressoNacional::with('sinodal')
                ->whereNotNull('sinodal_id')
                ->whereNull('federacao_id')
                ->orderBy('created_at', 'desc')
                ->get();

            $documentos = DocumentoRecebido::orderBy('created_at', 'desc')
                ->get();

            // Queries para quórum
            $totalizador = $this->getTotalizadorQuorum();

            return view('dashboard.congresso-nacional.executiva.index', [
                'delegadosFederacao' => $delegadosFederacao,
                'delegadosSinodal' => $delegadosSinodal,
                'documentos' => $documentos,
                'totalizador' => $totalizador
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    /**
     * Atualiza os campos pago e credencial de um delegado da federação
     */
    public function updateStatusDelegado(Request $request, DelegadoCongressoNacional $delegado): JsonResponse
    {
        $request->validate([
            'pago' => 'nullable|boolean',
            'credencial' => 'nullable|boolean',
        ]);

        try {
            $dados = [];

            if ($request->tipo == 'pago') {
                $dados['pago'] = $request->valor ? 1 : 0;
            }

            if ($request->tipo == 'credencial') {
                $dados['credencial'] = $request->valor ? 1 : 0;
            }

            if (empty($dados)) {
                return response()->json([
                    'status' => false,
                    'mensagem' => 'Nenhum dado para atualizar!'
                ], 500);
            }

            $delegado->update($dados);

            return response()->json([
                'status' => true,
                'mensagem' => 'Status do delegado atualizado com sucesso!'
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return response()->json([
                'status' => false,
                'mensagem' => $th->getMessage() ?? 'Erro ao atualizar status!'
            ], 500);
        }
    }

    public function updateStatusDocumento(Request $request, DocumentoRecebido $documento): JsonResponse
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        try {
            $documento->update(['status' => $request->status]);
            return response()->json([
                'status' => true,
                'mensagem' => 'Status do documento atualizado com sucesso!'
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return response()->json([
                'status' => false,
                'mensagem' => $th->getMessage() ?? 'Erro ao atualizar status do documento!'
            ], 500);
        }
    }

    /**
     * Sincroniza os inscritos do Congresso Nacional com a API externa
     */
    public function sincronizarInscritos(): RedirectResponse
    {
        try {
            // URL da API - pode ser configurada ou usar um ID de evento específico
            // Por enquanto, vamos usar um endpoint genérico para congresso
            $url = config('app.evento_url') . '/reuniao/listar-inscritos/f9e5d69f-09e1-4132-97e3-3915d0e73986';
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('app.evento_api_token')
            ];

            $response = Http::withHeaders($headers)->post($url);

            if ($response->failed()) {
                throw new \Exception("Erro ao sincronizar inscritos: " . $response->body());
            }

            $inscritos = $response->json();

            if (!is_array($inscritos)) {
                throw new \Exception("Resposta inválida da API");
            }

            $atualizados = 0;
            foreach ($inscritos as $inscrito) {
                $cpf = $this->formatarCpf($inscrito['cpf'] ?? '');

                if (empty($cpf)) {
                    continue;
                }

                // Buscar delegado pelo CPF (sem formatação para comparação)
                $cpfSemFormatacao = preg_replace('/[^0-9]/', '', $cpf);
                $delegado = DelegadoCongressoNacional::where('cpf', $cpfSemFormatacao)->first();

                if (empty($delegado)) {
                    continue;
                }

                // Verificar se o pagamento está confirmado
                $paymentStatus = $inscrito['payment_status'] ?? '';
                if (!in_array($paymentStatus, DelegadoCongressoNacional::STATUS_PAGAMENTO_CONFIRMADO)) {
                    continue;
                }

                // Atualizar delegado
                $dados = [
                    'pago' => true,
                    'telefone' => $inscrito['phone'] ?? $delegado->telefone
                ];

                // Atualizar status baseado na credencial
                if ($delegado->credencial) {
                    $dados['status'] = DelegadoCongressoNacional::STATUS_CONFIRMADA;
                } else {
                    $dados['status'] = DelegadoCongressoNacional::STATUS_EM_ANALISE;
                }

                $delegado->update($dados);
                $atualizados++;
            }

            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => "Inscritos sincronizados com sucesso! {$atualizados} delegado(s) atualizado(s)."
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao sincronizar inscritos!'
                ]
            ]);
        }
    }

    /**
     * Formata CPF no padrão 000.000.000-00
     *
     * @param string $cpf CPF sem formatação (apenas números)
     * @return string CPF formatado
     */
    private function formatarCpf(string $cpf): string
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

    /**
     * Lista sinodais ativas com suas federações e contadores de delegados confirmados
     */
    public function getSinodaisComFederacoesQuorum()
    {
        // Buscar sinodais ativas com região
        $sinodais = Sinodal::with('regiao:id,nome')
            ->select([
                'sinodais.id',
                'sinodais.nome',
                'sinodais.sigla',
                'sinodais.regiao_id'
            ])
            ->leftJoin('congresso_nacional_delegados as delegados_sinodal', function($join) {
                $join->on('delegados_sinodal.sinodal_id', '=', 'sinodais.id')
                     ->whereNull('delegados_sinodal.federacao_id')
                     ->where('delegados_sinodal.pago', '=', 1)
                     ->where('delegados_sinodal.credencial', '=', 1);
            })
            ->where('sinodais.status', 1)
            ->whereNull('sinodais.deleted_at')
            ->groupBy('sinodais.id', 'sinodais.nome', 'sinodais.sigla', 'sinodais.regiao_id')
            ->selectRaw('COUNT(delegados_sinodal.id) as total_delegados_sinodal')
            ->orderBy('sinodais.nome')
            ->get();

        // Para cada sinodal, buscar suas federações
        foreach ($sinodais as $sinodal) {
            $federacoes = Federacao::select([
                    'federacoes.id',
                    'federacoes.nome'
                ])
                ->leftJoin('congresso_nacional_delegados', function($join) {
                    $join->on('congresso_nacional_delegados.federacao_id', '=', 'federacoes.id')
                         ->where('congresso_nacional_delegados.pago', '=', 1)
                         ->where('congresso_nacional_delegados.credencial', '=', 1);
                })
                ->where('federacoes.sinodal_id', $sinodal->id)
                ->where('federacoes.status', 1)
                ->whereNull('federacoes.deleted_at')
                ->groupBy('federacoes.id', 'federacoes.nome')
                ->selectRaw('COUNT(congresso_nacional_delegados.id) as total_delegados')
                ->orderBy('federacoes.nome')
                ->get();

            $sinodal->federacoes = $federacoes;
        }

        return $sinodais;
    }

    /**
     * Calcula totalizador para quórum
     */
    public function getTotalizadorQuorum(): array
    {
        // Total de federações ativas
        $totalFederacoes = Federacao::where('status', 1)
            ->whereNull('deleted_at')
            ->count();

        // Total de sinodais ativas
        $totalSinodais = Sinodal::where('status', 1)
            ->whereNull('deleted_at')
            ->count();

        // Federações com ao menos 1 delegado confirmado
        $federacoesComDelegado = DB::table('federacoes')
            ->join('congresso_nacional_delegados', 'congresso_nacional_delegados.federacao_id', '=', 'federacoes.id')
            ->where('federacoes.status', 1)
            ->whereNull('federacoes.deleted_at')
            ->where('congresso_nacional_delegados.pago', 1)
            ->where('congresso_nacional_delegados.credencial', 1)
            ->distinct('federacoes.id')
            ->count('federacoes.id');

        // Sinodais com ao menos 1 delegado confirmado
        $sinodaisComDelegado = DB::table('sinodais')
            ->join('congresso_nacional_delegados', 'congresso_nacional_delegados.sinodal_id', '=', 'sinodais.id')
            ->where('sinodais.status', 1)
            ->whereNull('sinodais.deleted_at')
            ->whereNull('congresso_nacional_delegados.federacao_id')
            ->where('congresso_nacional_delegados.pago', 1)
            ->where('congresso_nacional_delegados.credencial', 1)
            ->distinct('sinodais.id')
            ->count('sinodais.id');

        // Cálculo de quórum
        $quorumSinodais = ceil(($totalSinodais * 0.5) + 1);
        $quorumFederacoes = ceil($totalFederacoes / 3);

        return [
            'total_federacoes' => $totalFederacoes,
            'total_sinodais' => $totalSinodais,
            'federacoes_com_delegado' => $federacoesComDelegado,
            'sinodais_com_delegado' => $sinodaisComDelegado,
            'quorum_sinodais' => $quorumSinodais,
            'quorum_federacoes' => $quorumFederacoes,
            'atingiu_quorum_sinodais' => $sinodaisComDelegado >= $quorumSinodais,
            'atingiu_quorum_federacoes' => $federacoesComDelegado >= $quorumFederacoes
        ];
    }
}
