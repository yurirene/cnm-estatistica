<?php

namespace App\Http\Controllers\Instancias;

use App\DataTables\Instancias\LocalDataTable;
use App\Http\Controllers\Controller;
use App\Models\Local;
use App\Services\Instancias\LocalService;
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

    public function updateInfo(Local $local, Request $request)
    {
        try {
            LocalService::updateInfo($local, $request);
            return redirect()->route('dashboard.home')->with([
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

    public function delete(Local $local)
    {
        try {
            LocalService::delete($local);
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
}
