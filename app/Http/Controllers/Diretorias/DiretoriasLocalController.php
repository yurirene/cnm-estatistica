<?php

namespace App\Http\Controllers\Diretorias;

use App\Http\Controllers\Controller;
use App\Models\Diretorias\DiretoriaFederacao;
use App\Models\Diretorias\DiretoriaLocal;
use App\Services\Instancias\DiretoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DiretoriasLocalController extends Controller
{
    public function index(): View
    {
        return view('dashboard.diretoria.index', [
            'tipo' => 'Local',
            'cargos' => DiretoriaService::getCargos(DiretoriaService::TIPO_DIRETORIA_LOCAL),
            'diretoria' => DiretoriaService::getDiretoria(DiretoriaService::TIPO_DIRETORIA_LOCAL, auth()->user()->local_id),
            'route' => 'dashboard.diretoria-local.update',
            'secretarias' => DiretoriaService::getSecretarios(),
        ]);
    }

    public function update(Request $request, DiretoriaLocal $diretoria): RedirectResponse
    {
        try {
            DiretoriaService::update($request->all(), $diretoria);
            return redirect()->route('dashboard.diretoria-local.index')->with([
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
