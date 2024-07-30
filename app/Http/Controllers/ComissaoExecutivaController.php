<?php

namespace App\Http\Controllers;

use App\DataTables\ComissaoExecutiva\CredenciaisDataTable;
use App\DataTables\ComissaoExecutiva\ReunioesDataTable;
use App\DataTables\ComissaoExecutiva\DocumentoRecebidoDataTable;
use App\Models\ComissaoExecutiva\Reuniao;
use App\Services\ComissaoExecutivaService;
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
        $credenciaisDataTable = new CredenciaisDataTable($reuniao->id);

        return $documentosDataTable->render('dashboard.comissao-executiva.show', [
            'reuniao' => $reuniao,
            'credenciaisDataTable' => $credenciaisDataTable->html()
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

    public function sinodal(DocumentoRecebidoDataTable $dataTable)
    {
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
                'arquivo' => 'mimes:pdf|max:300', // 300 Kb,
                'tipo' => 'required',
                'titulo' => 'required'
            ],
            [
                '*.mimes' => 'O arquivo precisa ser um PDF',
                '*.max' => 'O arquivo precisa ter no máximo 300 Kb',
                'tipo.required' => 'Selecione um tipo',
                'titulo.required' => 'O campo não pode estar vazio'
            ]
        );

        try {
            ComissaoExecutivaService::salvarDocumento($request->all());
            return redirect()->route('dashboard.ce-sinodal.index')->with([
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
            return redirect()->route('dashboard.ce-sinodal.index')->with([
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

    public function credenciaisDataTable(CredenciaisDataTable $credenciaisDataTable)
    {
        return $credenciaisDataTable->render('dashboard.comissao-executiva.show');
    }
}
