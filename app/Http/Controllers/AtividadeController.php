<?php

namespace App\Http\Controllers;

use App\Services\AtividadeService;
use App\Services\CalendarioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class AtividadeController extends Controller
{
    public function index()
    {
        // return $dataTable->render('dashboard.locais.index');    
        return view('dashboard.atividades.index');    
    }

    public function create()
    {
        $federacoes = AtividadeService::getFederacao();
        return view('dashboard.locais.form', [
            'federacoes' => $federacoes
        ]);
    }

    public function store(Request $request)
    {
        try {
            AtividadeService::store($request);
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
        $federacoes = AtividadeService::getFederacao();
        return view('dashboard.locais.form', [
            'federacoes' => $federacoes,
            'local' => $local,
        ]);
    }

    public function update(Local $local, Request $request)
    {
        try {
            AtividadeService::update($local, $request);
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

    public function calendario(Request $request)
    {
        return response()->json(CalendarioService::getCalendario($request));
    }
}
