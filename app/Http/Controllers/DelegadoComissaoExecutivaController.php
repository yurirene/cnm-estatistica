<?php

namespace App\Http\Controllers;

use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Services\ComissaoExecutivaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DelegadoComissaoExecutivaController extends Controller
{
    public function edit(string $delegado): View
    {
        $delegado = DelegadoComissaoExecutiva::findOrFail($delegado);

        return view('dashboard.comissao-executiva.delegado.index', [
            'delegado' => $delegado,
            'status' => DelegadoComissaoExecutiva::STATUS_LIST
        ]);
    }

    public function update(Request $request, DelegadoComissaoExecutiva $delegado)
    {
        try {
            ComissaoExecutivaService::updateDelegadoExecutiva($request->all(), $delegado);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Delegado atualizado com sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage()
                ]
            ]);
        }
    }
}
