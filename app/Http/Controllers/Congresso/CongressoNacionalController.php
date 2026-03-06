<?php

namespace App\Http\Controllers\Congresso;

use App\Helpers\FormHelper;
use App\Http\Controllers\Controller;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\CongressoNacional\DelegadoCongressoNacional;
use App\Models\CongressoNacional\DocumentoRecebido;
use App\Models\CongressoNacionalDocumentosInstancias;
use App\Models\CongressoReuniao;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\Sinodal;
use App\Rules\Cpf;
use App\Services\AvisoService;
use App\Services\DatatableAjaxService;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Instancias\DiretoriaService;
use App\Services\LogErroService;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            $reuniao = CongressoReuniao::aberta()->first();

            $queryDelegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)->whereNull('federacao_id');
            $queryDocumentos = DocumentoRecebido::where('sinodal_id', $sinodal->id);
            if ($reuniao) {
                $queryDelegados->where('reuniao_id', $reuniao->id);
                $queryDocumentos->where('reuniao_id', $reuniao->id);
            } else {
                $queryDelegados->whereNull('reuniao_id');
                $queryDocumentos->whereNull('reuniao_id');
            }

            $delegados = $queryDelegados->get();
            $totalDelegados = $delegados->count();
            $limiteAtingido = $totalDelegados >= 1;

            $documentos = $queryDocumentos->orderBy('created_at', 'desc')->get();

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
            $reuniao = CongressoReuniao::aberta()->first();

            $queryTotal = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)->whereNull('federacao_id');
            if ($reuniao) {
                $queryTotal->where('reuniao_id', $reuniao->id);
            } else {
                $queryTotal->whereNull('reuniao_id');
            }
            $totalDelegados = $queryTotal->count();
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
            $reuniao = CongressoReuniao::aberta()->first();

            $queryDelegados = DelegadoCongressoNacional::where('federacao_id', $federacao->id);
            $queryDocumentos = DocumentoRecebido::where('sinodal_id', $federacao->sinodal_id);
            if ($reuniao) {
                $queryDelegados->where('reuniao_id', $reuniao->id);
                $queryDocumentos->where('reuniao_id', $reuniao->id);
            } else {
                $queryDelegados->whereNull('reuniao_id');
                $queryDocumentos->whereNull('reuniao_id');
            }

            $delegados = $queryDelegados->get();
            $totalDelegados = $delegados->count();
            $limiteAtingido = $totalDelegados >= 6;

            $documentos = $queryDocumentos->orderBy('created_at', 'desc')->get();

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
            $reuniao = CongressoReuniao::aberta()->first();

            $queryTotal = DelegadoCongressoNacional::where('federacao_id', $federacao->id);
            if ($reuniao) {
                $queryTotal->where('reuniao_id', $reuniao->id);
            } else {
                $queryTotal->whereNull('reuniao_id');
            }
            $totalDelegados = $queryTotal->count();
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
            $reuniao = CongressoReuniao::aberta()->first();

            // Verificar limite de 6 delegados por federação (na reunião atual)
            $queryTotal = DelegadoCongressoNacional::where('federacao_id', $federacao->id);
            if ($reuniao) {
                $queryTotal->where('reuniao_id', $reuniao->id);
            } else {
                $queryTotal->whereNull('reuniao_id');
            }
            $totalDelegados = $queryTotal->count();
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
                'status' => DelegadoCongressoNacional::STATUS_EM_ANALISE,
                'reuniao_id' => $reuniao?->id,
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
            $reuniao = CongressoReuniao::aberta()->first();

            // Verificar limite de 1 delegado por sinodal (na reunião atual)
            $queryTotal = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)->whereNull('federacao_id');
            if ($reuniao) {
                $queryTotal->where('reuniao_id', $reuniao->id);
            } else {
                $queryTotal->whereNull('reuniao_id');
            }
            $totalDelegados = $queryTotal->count();
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
                'status' => DelegadoCongressoNacional::STATUS_EM_ANALISE,
                'reuniao_id' => $reuniao?->id,
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
            $reuniao = CongressoReuniao::aberta()->first();

            $documento = DocumentoRecebido::create([
                'titulo' => $request->titulo,
                'path' => $request->file('arquivo'),
                'sinodal_id' => $sinodal->id,
                'status' => DocumentoRecebido::STATUS_DOCUMENTO_PENDENTE,
                'reuniao_id' => $reuniao?->id,
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
     * Lista todos os delegados para o secretário executivo (da reunião aberta)
     */
    public function indexExecutiva()
    {
        try {
            $reuniao = CongressoReuniao::aberta()->first();

            $queryDelegados = DelegadoCongressoNacional::with(['federacao', 'sinodal']);
            $querySinodal = DelegadoCongressoNacional::with('sinodal')
                ->whereNotNull('sinodal_id')
                ->whereNull('federacao_id');
            $queryDocumentos = DocumentoRecebido::with('sinodal');

            if ($reuniao) {
                $queryDelegados->where('reuniao_id', $reuniao->id);
                $querySinodal->where('reuniao_id', $reuniao->id);
                $queryDocumentos->where('reuniao_id', $reuniao->id);
            } else {
                $queryDelegados->whereNull('reuniao_id');
                $querySinodal->whereNull('reuniao_id');
                $queryDocumentos->whereNull('reuniao_id');
            }

            $orderPrioridade = "CASE WHEN credencial = 0 AND pago = 1 THEN 0 WHEN credencial = 1 AND pago = 0 THEN 1 ELSE 2 END ASC, created_at desc";

            $delegadosFederacao = $queryDelegados
                ->whereNotNull('federacao_id')
                ->orderByRaw($orderPrioridade)
                ->get();

            $delegadosSinodal = $querySinodal
                ->orderByRaw($orderPrioridade)
                ->get();

            $documentos = $queryDocumentos
                ->orderBy('created_at', 'desc')
                ->get();

            $reuniaoId = $reuniao?->id;
            $documentosInstancias = CongressoNacionalDocumentosInstancias::with(['federacao', 'sinodal'])
                ->where('reuniao_id', $reuniaoId)
                ->orderByRaw('federacao_id IS NULL DESC')
                ->orderBy('sinodal_id')
                ->orderBy('federacao_id')
                ->get();

            $primeiroDelegadoPorInstancia = $this->getPrimeiroDelegadoPorInstancia($documentosInstancias, $reuniaoId);
            foreach ($documentosInstancias as $doc) {
                $chave = $doc->sinodal_id . '|' . ($doc->federacao_id ?? '');
                $doc->setRelation('primeiro_delegado', $primeiroDelegadoPorInstancia[$chave] ?? null);
            }

            $totalizador = $this->getTotalizadorQuorum($reuniaoId);

            return view('dashboard.congresso-nacional.executiva.index', [
                'reuniao' => $reuniao,
                'delegadosFederacao' => $delegadosFederacao,
                'delegadosSinodal' => $delegadosSinodal,
                'documentos' => $documentos,
                'documentosInstancias' => $documentosInstancias,
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
     * Retorna um mapa [ chave => primeiro delegado ] por instância (sinodal_id|federacao_id),
     * onde o delegado é o primeiro registrado (menor id) para aquela instância na reunião.
     */
    private function getPrimeiroDelegadoPorInstancia($documentosInstancias, $reuniaoId): array
    {
        if ($documentosInstancias->isEmpty()) {
            return [];
        }

        $query = DelegadoCongressoNacional::query()->orderBy('id');
        if ($reuniaoId !== null) {
            $query->where('reuniao_id', $reuniaoId);
        } else {
            $query->whereNull('reuniao_id');
        }

        $sinodalIds = $documentosInstancias->pluck('sinodal_id')->unique()->filter()->values();
        $query->whereIn('sinodal_id', $sinodalIds);

        $delegados = $query->get();

        $map = [];
        foreach ($delegados as $d) {
            $chave = $d->sinodal_id . '|' . ($d->federacao_id ?? '');
            if (!isset($map[$chave])) {
                $map[$chave] = $d;
            }
        }

        return $map;
    }

    /**
     * Exporta todos os delegados (da reunião aberta ou sem reunião) para CSV.
     */
    public function exportDelegadosCsv(): StreamedResponse
    {
        $reuniao = CongressoReuniao::aberta()->first();
        $query = DelegadoCongressoNacional::with(['federacao', 'sinodal']);

        if ($reuniao) {
            $query->where('reuniao_id', $reuniao->id);
        } else {
            $query->whereNull('reuniao_id');
        }

        $orderPrioridade = "CASE WHEN credencial = 0 AND pago = 1 THEN 0 WHEN credencial = 1 AND pago = 0 THEN 1 ELSE 2 END ASC";
        $delegados = $query->orderByRaw($orderPrioridade)->orderBy('credencial', 'asc')->get();

        $filename = 'delegados-congresso-nacional-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($delegados) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM para Excel
            fputcsv($out, ['Nome', 'CPF', 'Federação', 'Sinodal', 'Credencial', 'Pago', 'Comissoes', 'Regiao', 'Atualizado em'], ';');
            foreach ($delegados as $d) {
                fputcsv($out, [
                    $d->nome,
                    $d->cpf,
                    $d->federacao->nome ?? '-',
                    $d->sinodal->nome ?? '-',
                    $d->credencial ? 'Sim' : 'Não',
                    $d->pago ? 'Sim' : 'Não',
                    $d->comissoes ? implode(', ', $d->comissoes) : '-',
                    $d->sinodal->regiao->nome ?? '-',
                    $d->updated_at->format('d/m/Y H:i:s'),
                ], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Cadastra uma nova reunião e fecha as demais (status = 0)
     */
    public function storeReuniao(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        try {
            CongressoReuniao::where('status', true)->update(['status' => false]);
            CongressoReuniao::create([
                'nome' => $request->nome,
                'status' => true,
            ]);

            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Reunião cadastrada com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao cadastrar reunião!'
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
     * Sincroniza as instâncias (sinodais e federações) a partir dos delegados da reunião aberta
     * e cadastra/atualiza em congresso_nacional_documentos_instancias (unique: reuniao_id, sinodal_id, federacao_id).
     */
    public function sincronizarDocumentosInstancias(): RedirectResponse
    {
        try {
            $reuniao = CongressoReuniao::aberta()->first();
            $reuniaoId = $reuniao?->id;

            $query = DelegadoCongressoNacional::select('sinodal_id', 'federacao_id')
                ->whereNotNull('sinodal_id');

            if ($reuniaoId) {
                $query->where('reuniao_id', $reuniaoId);
            } else {
                $query->whereNull('reuniao_id');
            }

            $pares = $query->distinct()->get();
            $inseridos = 0;

            foreach ($pares as $par) {
                CongressoNacionalDocumentosInstancias::updateOrCreate(
                    [
                        'reuniao_id' => $reuniaoId,
                        'sinodal_id' => $par->sinodal_id,
                        'federacao_id' => $par->federacao_id,
                    ],
                    [
                        'reuniao_id' => $reuniaoId,
                        'sinodal_id' => $par->sinodal_id,
                        'federacao_id' => $par->federacao_id,
                    ]
                );
                $inseridos++;
            }

            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => "Sincronização concluída. {$inseridos} instância(s) atualizada(s)."
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao sincronizar documentos das instâncias.'
                ]
            ]);
        }
    }

    /**
     * Atualiza um campo (diretoria, estatistico, planejamento, status) de um registro de documento instância.
     */
    public function updateDocumentoInstancia(Request $request, CongressoNacionalDocumentosInstancias $documentoInstancia): JsonResponse
    {
        $request->validate([
            'campo' => 'required|string|in:diretoria,estatistico,planejamento,status',
            'valor' => 'required|boolean',
        ]);

        try {
            $campo = $request->campo;
            $documentoInstancia->update([$campo => $request->valor]);

            return response()->json([
                'status' => true,
                'mensagem' => 'Campo atualizado com sucesso!'
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return response()->json([
                'status' => false,
                'mensagem' => $th->getMessage() ?? 'Erro ao atualizar.'
            ], 500);
        }
    }

    /**
     * Exporta a tabela de documentos das instâncias (reunião aberta) para CSV.
     */
    public function exportDocumentosInstanciasCsv(): StreamedResponse
    {
        $reuniao = CongressoReuniao::aberta()->first();
        $reuniaoId = $reuniao?->id;

        $query = CongressoNacionalDocumentosInstancias::with(['federacao', 'sinodal'])
            ->where('reuniao_id', $reuniaoId)
            ->orderByRaw('federacao_id IS NULL DESC')
            ->orderBy('sinodal_id')
            ->orderBy('federacao_id');

        $itens = $query->get();

        $filename = 'documentos-instancias-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($itens) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM para Excel
            fputcsv($out, ['Instância', 'Tipo', 'Sinodal', 'Diretoria', 'Estatístico', 'Planejamento', 'Status', 'Região', 'Credenciado', 'Atualizado em'], ';');
            foreach ($itens as $doc) {
                $nome = $doc->federacao_id
                    ? ($doc->federacao->nome ?? '-')
                    : ($doc->sinodal->nome ?? '-');
                $tipo = $doc->federacao_id ? 'Federação' : 'Sinodal';
                $sinodalNome = $doc->sinodal->nome ?? '-';
                fputcsv($out, [
                    $nome,
                    $tipo,
                    $sinodalNome,
                    $doc->diretoria ? 'Sim' : 'Não',
                    $doc->estatistico ? 'Sim' : 'Não',
                    $doc->planejamento ? 'Sim' : 'Não',
                    $doc->status ? 'Sim' : 'Não',
                    $doc->sinodal->regiao->nome ?? '-',
                    $doc->delegado_para_exportacao?->credencial ? 'Sim' : 'Não',
                    $doc->updated_at->format('d/m/Y H:i:s'),
                ], ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
    public function getSinodaisComFederacoesQuorum(?int $reuniaoId = null)
    {
        // Buscar sinodais ativas com região
        $sinodais = Sinodal::with('regiao:id,nome')
            ->select([
                'sinodais.id',
                'sinodais.nome',
                'sinodais.sigla',
                'sinodais.regiao_id'
            ])
            ->leftJoin('congresso_nacional_delegados as delegados_sinodal', function($join) use ($reuniaoId) {
                $join->on('delegados_sinodal.sinodal_id', '=', 'sinodais.id')
                     ->whereNull('delegados_sinodal.federacao_id')
                     ->where('delegados_sinodal.pago', '=', 1)
                     ->where('delegados_sinodal.credencial', '=', 1)
                     ->where('delegados_sinodal.reuniao_id', $reuniaoId);
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
     * Calcula totalizador para quórum (opcionalmente filtrado por reunião)
     */
    public function getTotalizadorQuorum(?int $reuniaoId = null): array
    {
        // Total de federações ativas
        $totalFederacoes = Federacao::where('status', 1)
            ->whereNull('deleted_at')
            ->count();

        // Total de sinodais ativas
        $totalSinodais = Sinodal::where('status', 1)
            ->whereNull('deleted_at')
            ->count();

        $queryDelegadosFederacao = DB::table('federacoes')
            ->join('congresso_nacional_delegados', 'congresso_nacional_delegados.federacao_id', '=', 'federacoes.id')
            ->where('federacoes.status', 1)
            ->whereNull('federacoes.deleted_at')
            ->where('congresso_nacional_delegados.pago', 1)
            ->where('congresso_nacional_delegados.credencial', 1);

        $queryDelegadosSinodal = DB::table('sinodais')
            ->join('congresso_nacional_delegados', 'congresso_nacional_delegados.sinodal_id', '=', 'sinodais.id')
            ->where('sinodais.status', 1)
            ->whereNull('sinodais.deleted_at')
            ->whereNull('congresso_nacional_delegados.federacao_id')
            ->where('congresso_nacional_delegados.pago', 1)
            ->where('congresso_nacional_delegados.credencial', 1);

        if ($reuniaoId !== null) {
            $queryDelegadosFederacao->where('congresso_nacional_delegados.reuniao_id', $reuniaoId);
            $queryDelegadosSinodal->where('congresso_nacional_delegados.reuniao_id', $reuniaoId);
        } else {
            $queryDelegadosFederacao->whereNull('congresso_nacional_delegados.reuniao_id');
            $queryDelegadosSinodal->whereNull('congresso_nacional_delegados.reuniao_id');
        }

        $federacoesComDelegado = $queryDelegadosFederacao->distinct('federacoes.id')->count('federacoes.id');
        $sinodaisComDelegado = $queryDelegadosSinodal->distinct('sinodais.id')->count('sinodais.id');

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

    /**
     * Exclui um documento
     */
    public function deleteDocumento(string $documento): JsonResponse
    {
        try {
            $documento = DocumentoRecebido::findOrFail($documento);
            try {
                Storage::delete($documento->getRawOriginal('path'));
            } catch (\Throwable $th) {
                LogErroService::registrar([
                    'message' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'file' => $th->getFile()
                ]);
            }
            $documento->delete();

            return response()->json([
                'status' => true,
                'mensagem' => 'Documento excluído com sucesso!'
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return response()->json([
                'status' => false,
                'mensagem' => $th->getMessage() ?? 'Erro ao excluir documento!'
            ], 500);
        }
    }

    /**
     * Exporta a diretoria (sinodal ou federação) em PDF conforme o tipo do usuário logado,
     * usando o template resources/templates/diretoria.html.
     */
    public function exportDiretoria(DelegadoCongressoNacional $delegado): Response|RedirectResponse
    {
        try {
            $tipo = $delegado->federacao_id ? DiretoriaService::TIPO_DIRETORIA_FEDERACAO : DiretoriaService::TIPO_DIRETORIA_SINODAL;
            $instancia = $delegado->federacao ?? $delegado->sinodal;
            $instanciaId = $delegado->federacao_id ?? $delegado->sinodal_id;
            $diretoria = DiretoriaService::getDiretoria($tipo, $instanciaId);
            if (!$diretoria) {
                return redirect()->back()->with([
                    'mensagem' => ['status' => false, 'texto' => 'Diretoria não encontrada para esta instância.']
                ]);
            }

            $dados = DiretoriaService::getDiretoriaTabela($instanciaId, $tipo);
            $cargos = $dados['cargos'] ?? [];

            $labelSecretarioCausas = $tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL
                ? 'Secretário Sinodal'
                : 'Secretário Presbiterial';

            $replacements = [
                '{numero_oficio}' => '001',
                '{ano}' => now()->format('Y'),
                '{nome_instancia}' => $instancia->nome ?? '',
                '{nome_presidente}' => $cargos['Presidente']['nome'] ?? '',
                '{tel_presidente}' => $cargos['Presidente']['contato'] ?? '',
                '{nome_vice}' => $cargos['Vice-Presidente']['nome'] ?? '',
                '{tel_vice}' => $cargos['Vice-Presidente']['contato'] ?? '',
                '{nome_secretario_executivo}' => $cargos['Secretário-Executivo']['nome'] ?? '',
                '{tel_secretario_executivo}' => $cargos['Secretário-Executivo']['contato'] ?? '',
                '{nome_primeiro_secretario}' => $cargos['Primeiro(a) Secretário(a)']['nome'] ?? '',
                '{tel_primeiro_secretario}' => $cargos['Primeiro(a) Secretário(a)']['contato'] ?? '',
                '{nome_segundo_secretario}' => $cargos['Segundo(a) Secretário(a)']['nome'] ?? '',
                '{tel_segundo_secretario}' => $cargos['Segundo(a) Secretário(a)']['contato'] ?? '',
                '{nome_tesoureiro}' => $cargos['Tesoureiro(a)']['nome'] ?? '',
                '{tel_tesoureiro}' => $cargos['Tesoureiro(a)']['contato'] ?? '',
                '{nome_secretario_causas}' => $cargos[$labelSecretarioCausas]['nome'] ?? '',
                '{tel_secretario_causas}' => $cargos[$labelSecretarioCausas]['contato'] ?? '',
                '{data_atualizacao}' => $dados['atualizacao'] ?? now()->format('d/m/Y'),
            ];

            $estado = optional($instancia->estado ?? null)->nome
                ?? optional($instancia->regiao ?? null)->nome
                ?? '';
            $replacements['{cidade}'] = '';
            $replacements['{estado}'] = $estado;

            $templatePath = resource_path('templates/diretoria.html');
            if (!is_readable($templatePath)) {
                return redirect()->back()->with([
                    'mensagem' => ['status' => false, 'texto' => 'Template de diretoria não encontrado.']
                ]);
            }

            $html = file_get_contents($templatePath);
            $html = str_replace(array_keys($replacements), array_values($replacements), $html);

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $nomeArquivo = 'diretoria-' . ($tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL ? 'sinodal' : 'federacao')
                . '-' . now()->format('Y-m-d-His') . '.pdf';

            if (request()->boolean('exibir')) {
                return $pdf->stream($nomeArquivo, ['Attachment' => false]);
            }
            return $pdf->download($nomeArquivo);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao exportar diretoria.'
                ]
            ]);
        }
    }

    /**
     * Exporta o relatório estatístico do ano corrente em PDF usando o template
     * resources/templates/estatistico.html. Sinodal ou federação conforme o delegado.
     * Lança erro se não existir formulário estatístico do ano corrente.
     */
    public function exportRelatorioEstatistico(DelegadoCongressoNacional $delegado): Response|RedirectResponse
    {
        try {
            $tipo = $delegado->federacao_id ? DiretoriaService::TIPO_DIRETORIA_FEDERACAO : DiretoriaService::TIPO_DIRETORIA_SINODAL;
            $instancia = $delegado->federacao ?? $delegado->sinodal;
            $instanciaId = $delegado->federacao_id ?? $delegado->sinodal_id;

            $anoCorrente = EstatisticaService::getAnoReferencia();

            if ($tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL) {
                $formulario = FormularioSinodal::where('sinodal_id', $instanciaId)
                    ->where('ano_referencia', $anoCorrente)
                    ->first();
            } else {
                $formulario = FormularioFederacao::where('federacao_id', $instanciaId)
                    ->where('ano_referencia', $anoCorrente)
                    ->first();
            }

            if (!$formulario) {
                return redirect()->back()->with([
                    'mensagem' => [
                        'status' => false,
                        'texto' => "Não existe formulário estatístico para o ano corrente ({$anoCorrente}). Preencha o formulário antes de exportar."
                    ]
                ]);
            }

            $formulario->load($tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL ? 'sinodal.regiao' : 'federacao.estado');
            $perfil = $formulario->perfil ?? [];
            $escolaridade = $formulario->escolaridade ?? [];
            $estadoCivil = $formulario->estado_civil ?? [];
            $programacoes = $formulario->programacoes ?? [];
            $estrutura = $formulario->estrutura ?? [];

            $dadosDiretoria = DiretoriaService::getDiretoriaTabela((string) $instanciaId, $tipo);
            $nomePresidente = $dadosDiretoria['cargos']['Presidente']['nome'] ?? '';

            $estado = $tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL
                ? (optional($instancia->regiao)->nome ?? '')
                : (optional($instancia->estado)->nome ?? '');

            $umpsOrg = $estrutura['ump_organizada'] ?? '0';
            $umpsNaoOrg = $estrutura['ump_nao_organizada'] ?? '0';
            $fedOrg = $tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL
                ? ($estrutura['federacao_organizada'] ?? '0')
                : '1';
            $fedNaoOrg = $tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL
                ? ($estrutura['federacao_nao_organizada'] ?? '0')
                : '0';

            $replacements = [
                '{ano_referencia}' => (string) $formulario->ano_referencia,
                '{numero_documento}' => '001',
                '{ano}' => now()->format('Y'),
                '{ativos}' => (string) ($perfil['ativos'] ?? '0'),
                '{cooperadores}' => (string) ($perfil['cooperadores'] ?? '0'),
                '{menores_19}' => (string) ($perfil['menor19'] ?? '0'),
                '{entre_19_23}' => (string) ($perfil['de19a23'] ?? '0'),
                '{entre_24_29}' => (string) ($perfil['de24a29'] ?? '0'),
                '{entre_30_35}' => (string) ($perfil['de30a35'] ?? '0'),
                '{homens}' => (string) ($perfil['homens'] ?? '0'),
                '{mulheres}' => (string) ($perfil['mulheres'] ?? '0'),
                '{fundamental}' => (string) ($escolaridade['fundamental'] ?? '0'),
                '{medio}' => (string) ($escolaridade['medio'] ?? '0'),
                '{tecnico}' => (string) ($escolaridade['tecnico'] ?? '0'),
                '{superior}' => (string) ($escolaridade['superior'] ?? '0'),
                '{pos}' => (string) ($escolaridade['pos'] ?? '0'),
                '{solteiros}' => (string) ($estadoCivil['solteiros'] ?? '0'),
                '{casados}' => (string) ($estadoCivil['casados'] ?? '0'),
                '{divorciados}' => (string) ($estadoCivil['divorciados'] ?? '0'),
                '{viuvos}' => (string) ($estadoCivil['viuvos'] ?? '0'),
                '{com_filhos}' => (string) ($estadoCivil['filhos'] ?? '0'),
                '{social}' => (string) ($programacoes['social'] ?? '0'),
                '{evangelistico}' => (string) ($programacoes['evangelistico'] ?? '0'),
                '{espiritual}' => (string) ($programacoes['espiritual'] ?? '0'),
                '{recreativo}' => (string) ($programacoes['recreativo'] ?? '0'),
                '{oracao}' => (string) ($programacoes['oracao'] ?? '0'),
                '{umps_org}' => (string) $umpsOrg,
                '{umps_nao_org}' => (string) $umpsNaoOrg,
                '{fed_org}' => (string) $fedOrg,
                '{fed_nao_org}' => (string) $fedNaoOrg,
                '{nome_presidente}' => $nomePresidente,
                '{cidade}' => '',
                '{estado}' => $estado,
                '{data_emissao}' => $formulario->updated_at->format('d/m/Y'),
                'status_entrega' => FormHelper::statusFormatado($formulario->status, 'Entregue', 'Pendente'),
                '{nome_instancia}' => $instancia->nome ?? '',
            ];

            $templatePath = resource_path('templates/estatistico.html');
            if (!is_readable($templatePath)) {
                return redirect()->back()->with([
                    'mensagem' => ['status' => false, 'texto' => 'Template do relatório estatístico não encontrado.']
                ]);
            }

            $html = file_get_contents($templatePath);
            $html = str_replace(array_keys($replacements), array_values($replacements), $html);

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $nomeArquivo = 'relatorio-estatistico-' . ($tipo === DiretoriaService::TIPO_DIRETORIA_SINODAL ? 'sinodal' : 'federacao')
                . '-' . $anoCorrente . '-' . now()->format('Y-m-d-His') . '.pdf';

            if (request()->boolean('exibir')) {
                return $pdf->stream($nomeArquivo, ['Attachment' => false]);
            }
            return $pdf->download($nomeArquivo);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?? 'Erro ao exportar relatório estatístico.'
                ]
            ]);
        }
    }
}
