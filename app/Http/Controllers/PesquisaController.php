<?php

namespace App\Http\Controllers;

use App\DataTables\PesquisaDataTable;
use App\Services\PesquisaService;
use Illuminate\Http\Request;
use Throwable;

class PesquisaController extends Controller
{
    public function index(PesquisaDataTable $dataTable)
    {
        return $dataTable->render('dashboard.pesquisas.index');    
    }

    public function create()
    {
        return view('dashboard.pesquisas.form', []);
    }

    public function store(Request $request)
    {
        try {
            PesquisaService::store($request);
            return redirect()->route('dashboard.pesquisas.index')->with([
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
