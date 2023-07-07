<?php

namespace App\Http\Controllers\Instancias;

use App\DataTables\Instancias\SinodalDataTable;
use App\Http\Controllers\Controller;
use App\Models\Regiao;
use App\Models\Sinodal;
use App\Services\Instancias\SinodalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SinodalController extends Controller
{
    public function index(SinodalDataTable $dataTable)
    {
        return $dataTable->render('dashboard.sinodais.index');
    }

    public function create()
    {
        return view('dashboard.sinodais.form', [
            'regiao' => strtolower(Auth::user()->regioes->first()->nome),
            'regioes' => Regiao::get()->pluck('nome', 'id')
        ]);
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
            'federacoes' => SinodalService::getInformacoesFederacoesShow($sinodal),
            'informacoes' => SinodalService::getInformacoesOrganizacao($sinodal),
            'sinodal' => $sinodal,
        ]);
    }

    public function edit(Sinodal $sinodal)
    {
        $estados = SinodalService::getEstados();
        return view('dashboard.sinodais.form', [
            'sinodal' => $sinodal,
            'estados' => $estados,
            'regiao' => strtolower(Auth::user()->regioes->first()->nome),
            'regioes' => Regiao::get()->pluck('nome', 'id')
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

    public function updateInfo(Sinodal $sinodal, Request $request)
    {
        try {
            SinodalService::updateInfo($sinodal, $request);
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

    public function delete(Sinodal $sinodal)
    {
        try {
            SinodalService::delete($sinodal);
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
