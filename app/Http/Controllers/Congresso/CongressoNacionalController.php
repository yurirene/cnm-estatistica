<?php

namespace App\Http\Controllers\Congresso;

use App\Http\Controllers\Controller;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\CongressoNacional\DelegadoCongressoNacional;
use App\Models\CongressoNacional\DocumentoRecebido;
use App\Rules\Cpf;
use App\Services\AvisoService;
use App\Services\DatatableAjaxService;
use App\Services\LogErroService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CongressoNacionalController extends Controller
{
    public function indexSinodal()
    {
        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();
            $delegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)
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
            'credencial_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
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
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
        ]);

        try {
            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'oficial' => $request->oficial,
            ];

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
            'credencial_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'cpf' => ['required', 'string', new Cpf()],
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'cpf.required' => 'O CPF é obrigatório',
        ]);

        try {
            $sinodal = UserService::getInstanciaUsuarioLogado();

            // Verificar limite de 1 delegado por sinodal
            $totalDelegados = DelegadoCongressoNacional::where('sinodal_id', $sinodal->id)
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
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'oficial.required' => 'O campo oficial é obrigatório',
            'credencial_file.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial_file.max' => 'A credencial deve ter no máximo 2MB',
            'cpf.required' => 'O CPF é obrigatório',
        ]);

        try {
            $dados = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'oficial' => $request->oficial,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            ];

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

            return view('dashboard.congresso-nacional.executiva.index', [
                'delegadosFederacao' => $delegadosFederacao,
                'delegadosSinodal' => $delegadosSinodal
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
    public function updateStatusFederacao(Request $request, DelegadoCongressoNacional $delegado): RedirectResponse
    {
        $request->validate([
            'pago' => 'nullable|boolean',
            'credencial' => 'nullable|boolean',
        ]);

        try {
            $dados = [];

            if ($request->has('pago')) {
                $dados['pago'] = $request->pago ? 1 : 0;
            }

            if ($request->has('credencial')) {
                $dados['credencial'] = $request->credencial ? 1 : 0;
            }

            if (!empty($dados)) {
                $delegado->update($dados);
            }

            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Status do delegado atualizado com sucesso!'
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
                    'texto' => $th->getMessage() ?? 'Erro ao atualizar status!'
                ]
            ]);
        }
    }

    /**
     * Atualiza os campos pago e credencial de um delegado da sinodal
     */
    public function updateStatusSinodal(Request $request, DelegadoCongressoNacional $delegado): RedirectResponse
    {
        $request->validate([
            'pago' => 'nullable|boolean',
            'credencial' => 'nullable|boolean',
        ]);

        try {
            $dados = [];

            if ($request->has('pago')) {
                $dados['pago'] = $request->pago ? 1 : 0;
            }

            if ($request->has('credencial')) {
                $dados['credencial'] = $request->credencial ? 1 : 0;
            }

            if (!empty($dados)) {
                $delegado->update($dados);
            }

            return redirect()->route('dashboard.cn.executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Status do delegado atualizado com sucesso!'
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
                    'texto' => $th->getMessage() ?? 'Erro ao atualizar status!'
                ]
            ]);
        }
    }
}
