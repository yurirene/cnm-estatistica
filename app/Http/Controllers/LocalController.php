<?php

namespace App\Http\Controllers;

use App\DataTables\LocalDataTable;
use App\Models\Local;
use App\Services\LocalService;
use Illuminate\Http\Request;
use Throwable;

class LocalController extends Controller
{
    
    public function index(LocalDataTable $dataTable)
    {
        return $dataTable->render('dashboard.locais.index');        
    }

    public function create()
    {
        return view('dashboard.locais.form');
    }

    public function store(Request $request)
    {
        try {
            LocalService::store($request);
            return redirect()->route('dashboard.locais.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
                ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }

    public function show(Local $local)
    {
        return view('dashboard.locais.show', [
            'local' => $local,
        ]);
    }

    public function edit(Local $local)
    {
        return view('dashboard.locais.form', [
            'local' => $local,
        ]);
    }

    public function update(Local $local, Request $request)
    {
        try {
            LocalService::update($local, $request);
            return redirect()->route('dashboard.locais.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }


    public function delete(Local $federacao)
    {
        try {
            LocalService::delete($federacao);
            return redirect()->route('dashboard.sinodais.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
}
