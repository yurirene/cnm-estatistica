<?php

namespace App\Http\Controllers;

use App\DataTables\ComissaoExecutivaDataTable;
use Illuminate\Http\Request;

class ComissaoExecutivaController extends Controller
{
    public function index(ComissaoExecutivaDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.comissao-executiva.index');
        } catch (\Throwable $th) {
            dd($th->getMessage());
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
            AvisoService::store($request->all());
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Aviso criado com sucesso!'
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
}
