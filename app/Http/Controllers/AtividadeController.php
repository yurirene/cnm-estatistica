<?php

namespace App\Http\Controllers;

use App\DataTables\AtividadesDataTable;
use App\Helpers\AuthHelper;
use App\Models\Atividade;
use App\Services\AtividadeService;
use App\Services\CalendarioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class AtividadeController extends Controller
{
    public function index(AtividadesDataTable $dataTable)
    {
        return $dataTable->render('dashboard.atividades.index');    
    }

    public function create()
    {
        return view('dashboard.atividades.form', [
            'tipos' => Atividade::TIPOS
        ]);
    }

    public function store(Request $request)
    {
        try {
            AtividadeService::store($request);
            return redirect()->route('dashboard.atividades.index')->with([
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

    public function edit(Atividade $atividade)
    {
        return view('dashboard.atividades.form', [
            'atividade' => $atividade,
            'tipos' => Atividade::TIPOS
        ]);
    }

    public function confirmar(Atividade $atividade)
    {
        try {
            AtividadeService::confirmar($atividade);
            return redirect()->route('dashboard.atividades.index')->with([
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

    public function update(Atividade $atividade, Request $request)
    {
        try {
            AtividadeService::update($atividade, $request);
            return redirect()->route('dashboard.atividades.index')->with([
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

    public function delete(Atividade $atividade)
    {
        try {
            AtividadeService::delete($atividade);
            return redirect()->route('dashboard.atividades.index')->with([
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
