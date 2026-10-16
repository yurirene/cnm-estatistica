<?php

namespace App\Http\Controllers;

use App\DataTables\DigestoDataTable;
use App\Models\Digesto;
use App\Services\DigestoService;
use Illuminate\Http\Request;
use Throwable;

class DigestoController extends Controller
{
    public function index(DigestoDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.digestos.index', [
                'tipos' => DigestoService::getTipos(),
                'anos' => DigestoService::getAnos(),
                'completude' => DigestoService::contagemCompletude(),
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

    public function create()
    {
        try {
            return view('dashboard.digestos.form', [
                'tipos' => DigestoService::getTipos(),
                'tiposDocumento' => DigestoService::getTiposDocumento(),
                'comissoes' => DigestoService::getComissoes(),
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

    public function store(Request $request)
    {
        try {
            DigestoService::store($request);
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }


    public function edit(Digesto $digesto)
    {
        try {
            return view('dashboard.digestos.form', [
                'tipos' => DigestoService::getTipos(),
                'tiposDocumento' => DigestoService::getTiposDocumento(),
                'comissoes' => DigestoService::getComissoes(),
                'arquivo' => DigestoService::dadosArquivo($digesto),
                'digesto' => $digesto,
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

    public function update(Digesto $digesto, Request $request)
    {
        try {
            DigestoService::update($digesto, $request);
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function delete(Digesto $digesto)
    {
        try {
            DigestoService::delete($digesto);
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.digestos.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function digesto()
    {
        return view('digesto.index', array_merge(DigestoService::buscar(), [
            'tipos' => DigestoService::getTipos(),
            'tiposDocumento' => DigestoService::getTiposDocumento(),
        ]));
    }

    public function exportar()
    {
        if (! DigestoService::temFiltrosAtivos()) {
            return redirect()->route('digesto');
        }

        return DigestoService::exportarCsv();
    }

    public function exibir(string $path)
    {
        return DigestoService::exibir($path);
    }
}
