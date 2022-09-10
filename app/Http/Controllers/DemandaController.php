<?php

namespace App\Http\Controllers;

use App\DataTables\DemandasDataTable;
use App\DataTables\DemandasItemDataTable;
use App\Models\Demanda;
use App\Models\DemandaItem;
use App\Services\DemandasService;
use Illuminate\Http\Request;
use Throwable;

class DemandaController extends Controller
{
    public function index(DemandasDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.demandas.index');
        } catch (Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function create()
    {
        try {
            return view('dashboard.demandas.form');
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
    public function informacoesAdicionais(Request $request)
    {
        try {
            $campos = DemandasService::getCampos($request);

            return view('dashboard.demandas.form', [
                'campos' => $campos['informacoes'],
                'titulo' => $request->titulo,
                'path' => $campos['path'],
                'usuarios' => DemandasService::getUsuarios()
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function store(Request $request)
    {
        try {
            DemandasService::store($request);
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function show(Demanda $demanda, DemandasItemDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.demandas.show', [
                'demanda' => $demanda,
                'usuarios' => DemandasService::getUsuarios(),
                'status' => DemandasService::getStatus(),
                'niveis' => DemandasService::getNiveis(),
                'totalizadores' => DemandasService::totalizadores($demanda)
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function edit(Demanda $demanda)
    {
        try {
            return view('dashboard.demandas.form', [
                'demanda' => $demanda
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function update(Demanda $demanda, Request $request)
    {
        try {
            DemandasService::update($demanda, $request);
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function delete(Demanda $demanda)
    {
        try {
            DemandasService::delete($demanda);
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function atualizarItem(Demanda $demanda, DemandaItem $item, Request $request)
    {

        try {
            DemandasService::atualizarItem($item, $request->all());
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function storeItem(Demanda $demanda, Request $request)
    {

        try {
            DemandasService::storeItem($demanda, $request->all());
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function deleteItem(Demanda $demanda, DemandaItem $item)
    {

        try {
            DemandasService::deleteItem($item);
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.demandas.show', $demanda->id)->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

}
