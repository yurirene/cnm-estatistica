<?php

namespace App\Http\Controllers;

use App\DataTables\ComprovanteAciDataTable;
use App\Services\ComprovanteAciService;
use Illuminate\Http\Request;
use Throwable;

class ComprovanteACIController extends Controller
{
    public function index(ComprovanteAciDataTable $dataTable)
    {
        return $dataTable->render('dashboard.comprovante-aci.index');
    }

    public function store(Request $request)
    {
        try {
            ComprovanteAciService::store($request);
            return redirect()->route('dashboard.comprovante-aci.index')->with([
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
