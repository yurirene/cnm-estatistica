<?php

namespace App\Http\Controllers\Instancias;

use App\DataTables\Instancias\SinodalDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSinodalRequest;
use App\Models\Regiao;
use App\Models\Sinodal;
use App\Services\Instancias\DiretoriaService;
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
            'regiao' => strtolower(auth()->user()->regiao->nome),
            'regioes' => Regiao::get()->pluck('nome', 'id')
        ]);
    }

    public function store(StoreSinodalRequest $request)
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
            'navegacaoSinodais' => SinodalService::navegacaoListaSinodais($sinodal->id),
            'diretoria' => DiretoriaService::getDiretoriaTabela($sinodal->id, DiretoriaService::TIPO_DIRETORIA_SINODAL)
        ]);
    }

    public function edit(Sinodal $sinodal)
    {
        $estados = SinodalService::getEstados();
        return view('dashboard.sinodais.form', [
            'sinodal' => $sinodal,
            'estados' => $estados,
            'regiao' => auth()->user()->admin ? '' : strtolower(auth()->user()->regiao->nome),
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
