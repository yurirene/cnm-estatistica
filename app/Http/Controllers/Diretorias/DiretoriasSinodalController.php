<?php

namespace App\Http\Controllers\Diretorias;

use App\Http\Controllers\Controller;
use App\Models\Diretorias\DiretoriaSinodal;
use App\Services\Instancias\DiretoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DiretoriasSinodalController extends Controller
{
    public function index(): View
    {
        return view('dashboard.diretoria.index', [
            'tipo' => 'Sinodal',
            'cargos' => DiretoriaService::getCargos(DiretoriaService::TIPO_DIRETORIA_SINODAL),
            'diretoria' => DiretoriaService::getDiretoria(DiretoriaService::TIPO_DIRETORIA_SINODAL, auth()->user()->sinodal_id),
            'route' => 'dashboard.diretoria-sinodal.update',
            'secretarias' => DiretoriaService::getSecretarios(),
        ]);
    }

    public function update(Request $request, DiretoriaSinodal $diretoria): RedirectResponse
    {
        try {
            DiretoriaService::update($request->all(), $diretoria);
            return redirect()->route('dashboard.diretoria-sinodal.index')->with([
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
