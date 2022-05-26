<?php

namespace App\Http\Controllers;

use App\DataTables\SinodalDataTable;
use App\Models\Sinodal;
use App\Services\SinodalService;
use Illuminate\Http\Request;
use Throwable;

class SinodalController extends Controller
{
    public function index(SinodalDataTable $dataTable)
    {
        return $dataTable->render('dashboard.sinodais.index');        
    }

    public function create()
    {
        return view('dashboard.sinodais.form');
    }

    public function store(Request $request)
    {
        try {
            SinodalService::store($request);
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

    public function show(Sinodal $sinodal)
    {
        return view('dashboard.sinodais.show', [
            'sinodal' => $sinodal,
        ]);
    }

    public function edit(Sinodal $sinodal)
    {
        $estados = SinodalService::getEstados();
        return view('dashboard.sinodais.form', [
            'sinodal' => $sinodal,
            'estados' => $estados
        ]);
    }

    public function update(Sinodal $sinodal, Request $request)
    {
        try {
            SinodalService::update($sinodal, $request);
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
