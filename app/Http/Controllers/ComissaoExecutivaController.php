<?php

namespace App\Http\Controllers;

use App\DataTables\ComissaoExecutiva\DelegadosDataTable;
use App\DataTables\ComissaoExecutiva\ReunioesDataTable;
use App\DataTables\ComissaoExecutiva\DocumentoRecebidoDataTable;
use App\DataTables\ComissaoExecutiva\Sinodais\ReunioesCEDataTable;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\ComissaoExecutiva\Reuniao;
use App\Services\ComissaoExecutivaService;
use App\Services\LogErroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComissaoExecutivaController extends Controller
{
    public function index(ReunioesDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.comissao-executiva.index',[
                'isSinodal' => false
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function create(): View
    {
        return view('dashboard.comissao-executiva.form');
    }

    public function edit(Reuniao $reuniao): View
    {
        return view('dashboard.comissao-executiva.form', [
            'reuniao' => $reuniao
        ]);
    }

    public function show(Reuniao $reuniao)
    {
        $documentosDataTable = new DocumentoRecebidoDataTable($reuniao->id);
        $delegadosDataTable = new DelegadosDataTable($reuniao->id);

        return $documentosDataTable->render('dashboard.comissao-executiva.show', [
            'reuniao' => $reuniao,
            'delegadosDataTable' => $delegadosDataTable->html()
        ]);
    }

    public function sincronizarInscritos(Reuniao $reuniao)
    {
        ComissaoExecutivaService::sincronizarInscritos($reuniao);
        return redirect()->back()->with([
            'mensagem' => [
                'status' => true,
                'texto' => 'Inscritos sincronizados com sucesso!'
            ]
        ]);
    }


    public function store(Request $request): RedirectResponse
    {
        try {
            ComissaoExecutivaService::store($request->all());
            return redirect()->route('dashboard.comissao-executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Reunião criada com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }


    public function update(Request $request, Reuniao $reuniao): RedirectResponse
    {
        try {
            ComissaoExecutivaService::update($request->all(), $reuniao);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Dados atualizados com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }


    public function delete(Reuniao $reuniao): RedirectResponse
    {
        try {
            ComissaoExecutivaService::delete($reuniao);
            return redirect()->route('dashboard.comissao-executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Reunião removida com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }
    public function encerrar(Reuniao $reuniao): RedirectResponse
    {
        try {
            ComissaoExecutivaService::encerrar($reuniao);
            return redirect()->route('dashboard.comissao-executiva.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Reunião removida com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function sinodal()
    {
        $dataTable = new ReunioesCEDataTable();
        $reuniao = ComissaoExecutivaService::getReuniaoAberta();

        return $dataTable->render('dashboard.comissao-executiva.index', [
            'reuniao' => $reuniao,
            'isSinodal' => true,
            'tipos' => ["" => "Selecione um tipo"] + ComissaoExecutivaService::getTiposDocumentos()
        ]);
    }


    public function enviarDocumento(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'arquivo' => 'mimes:pdf|max:1000', // 1 Mb,
                'titulo' => 'required'
            ],
            [
                '*.mimes' => 'O arquivo precisa ser um PDF',
                '*.max' => 'O arquivo precisa ter no máximo 1 Mb',
                'titulo.required' => 'O campo não pode estar vazio'
            ]
        );

        try {
            ComissaoExecutivaService::salvarDocumento($request->all());
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Documento Salvo com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }

    public function removerDocumento(string $documento): RedirectResponse
    {
        try {
            ComissaoExecutivaService::removerDocumento($documento);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Documento Removido com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }

    public function confirmarDocumento(string $documento): RedirectResponse
    {
        try {
            ComissaoExecutivaService::confirmarDocumento($documento);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Alteração de Recebimento realizado!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }

    public function delegadosDataTable(string $reuniao)
    {
        $delegadosDataTable = new DelegadosDataTable($reuniao);
        return $delegadosDataTable->render('dashboard.comissao-executiva.show');
    }

    public function showSinodal(string $reuniao)
    {
        $dataTable = new DocumentoRecebidoDataTable($reuniao);
        return $dataTable->render('dashboard.comissao-executiva.show-sinodal', [
            'delegado' => ComissaoExecutivaService::getDelegado(),
            'suplente' => ComissaoExecutivaService::getDelegadoSuplente(),
            'reuniao' => ComissaoExecutivaService::getReuniaoAberta(),
            'documentosAutomaticos' => ComissaoExecutivaService::getDocumentosAutomaticosEntregues($reuniao)
        ]);
    }

    public function storeDelegado(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'credencial' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'credencial.required' => 'A credencial é obrigatória',
            'credencial.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial.max' => 'A credencial deve ter no máximo 2MB'
        ]);

        try {
            ComissaoExecutivaService::storeDelegado($request->all());
            return redirect()->back()->with([
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
                    'texto' => $th->getMessage()
                ]
            ])
            ->withInput();
        }
    }

    public function updateDelegado(Request $request, DelegadoComissaoExecutiva $delegado): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'credencial' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'nome.required' => 'O nome do delegado é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'credencial.mimes' => 'A credencial deve ser um arquivo PDF ou imagem',
            'credencial.max' => 'A credencial deve ter no máximo 2MB'
        ]);

        try {
            ComissaoExecutivaService::updateDelegado($request->all(), $delegado);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado atualizado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ])
            ->withInput();
        }
    }
}
