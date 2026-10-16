<?php

namespace App\Http\Controllers;

use App\DataTables\ComprovanteAciDataTable;
use App\Models\ComprovanteACI;
use App\Models\Parametro;
use App\Services\ComprovanteAciService;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\GamificacaoHook;
use Illuminate\Http\Request;
use Throwable;

class ComprovanteACIController extends Controller
{
    public function index(ComprovanteAciDataTable $dataTable)
    {
        return $dataTable->render('dashboard.comprovante-aci.index', [
            'ano' => EstatisticaService::getAnoReferencia(),
            'filtros' => $dataTable->filtros()
        ]);
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

    public function status(ComprovanteACI $comprovante)
    {
        try {
            ComprovanteAciService::alterarStatus($comprovante);
            GamificacaoHook::aposAci((string) $comprovante->sinodal_id);
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

    public function metaAtingida(ComprovanteACI $comprovante)
    {
        try {
            ComprovanteAciService::marcarMetaAtingida($comprovante);
            GamificacaoHook::aposAci((string) $comprovante->sinodal_id);
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
