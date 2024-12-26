<?php

namespace App\Http\Controllers\Diretorias;

use App\Http\Controllers\Controller;
use App\Models\Diretorias\DiretoriaFederacao;
use App\Services\Instancias\DiretoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DiretoriasFederacaoController extends Controller
{
    public function index(): View
    {
        return view('dashboard.diretoria.index', [
            'tipo' => 'Federação',
            'cargos' => DiretoriaService::getCargos(DiretoriaService::TIPO_DIRETORIA_FEDERACAO),
            'diretoria' => DiretoriaService::getDiretoria(DiretoriaService::TIPO_DIRETORIA_FEDERACAO),
            'route' => 'dashboard.diretoria-federacao.update',
            'secretarias' => DiretoriaService::getSecretarios(),
        ]);
    }

    public function update(Request $request, DiretoriaFederacao $diretoria): RedirectResponse
    {
        try {
            DiretoriaService::update($request->all(), $diretoria);
            return redirect()->route('dashboard.diretoria-federacao.index')->with([
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
