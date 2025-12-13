<?php

namespace App\Http\Controllers;

use App\DataTables\Instancias\FederacaoDataTable;
use App\DataTables\Instancias\LocalDataTable;
use App\DataTables\TransferenciasFederacoesDataTable;
use App\DataTables\TransferenciasUmpsDataTable;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\TransferenciaUnidade;
use App\Services\LogErroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TransferenciasController extends Controller
{
    public function index()
    {
        if (auth()->user()->role->name == 'diretoria' && !request()->filled('ump')) {
            return (new FederacaoDataTable(true))->render('dashboard.trasferencias.index', [
                'sinodais' => Sinodal::query()->minhaRegiao()->get()->pluck('nome', 'id')->toArray(),
                'rotaUpdate' => route('dashboard.transferencias.transferir-federacao'),
                'showFederacao' => true,
            ]);
        } else if (in_array(auth()->user()->role->name, ['sinodal', 'diretoria']) && request()->filled('ump')) {
            if (auth()->user()->role->name == 'sinodal') {
                $federacoes = Federacao::query()->minhaSinodal()->get()->pluck('nome', 'id')->toArray();
            } else {
                $federacoes = Federacao::query()->daMinhaRegiao()->get()->pluck('nome', 'id')->toArray();
            }

            return (new LocalDataTable(true))->render('dashboard.trasferencias.index', [
                'federacoes' => $federacoes,
                'rotaUpdate' => route('dashboard.transferencias.transferir-ump'),
                'showFederacao' => auth()->user()->role->name == 'diretoria',
            ]);
        }
    }

    public function transferirFederacao(Request $request)
    {
        DB::beginTransaction();
        try {
            $federacao = Federacao::findOrFail($request->instancia_id);
            TransferenciaUnidade::create([
                'federacao_id' => $federacao->id,
                'sinodal_origem_id' => $federacao->sinodal_id,
                'sinodal_destino_id' => $request->sinodal_destino_id,
                'user_id' => auth()->user()->id,
            ]);
            $federacao->updateOrFail([
                'sinodal_id' => $request->sinodal_destino_id,
            ]);
            $umps = Local::where('federacao_id', $federacao->id)->get();

            foreach ($umps as $ump) {
                TransferenciaUnidade::create([
                    'local_id' => $ump->id,
                    'sinodal_origem_id' => $ump->sinodal_id,
                    'sinodal_destino_id' => $request->sinodal_destino_id,
                    'user_id' => auth()->user()->id,
                ]);
                $ump->updateOrFail([
                    'sinodal_id' => $request->sinodal_destino_id,
                ]);
            }

            DB::commit();

            Artisan::call('cache:clear');

            return redirect()->route('dashboard.transferencias.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogErroService::registrar([
                'msg' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->route('dashboard.transferencias.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function transferirUmp(Request $request)
    {
        DB::beginTransaction();
        try {
            $ump = Local::findOrFail($request->instancia_id);
            $federacaoDestino = Federacao::findOrFail($request->federacao_destino_id);
            
            TransferenciaUnidade::create([
                'local_id' => $ump->id,
                'federacao_origem_id' => $ump->federacao_id,
                'federacao_destino_id' => $federacaoDestino->id,
                'sinodal_origem_id' => $ump->sinodal_id,
                'sinodal_destino_id' => $federacaoDestino->sinodal_id,
                'user_id' => auth()->user()->id,
            ]);

            $ump->updateOrFail([
                'federacao_id' => $federacaoDestino->id,
                'sinodal_id' => $federacaoDestino->sinodal_id,
            ]);

            DB::commit();

            Artisan::call('cache:clear');

            return redirect()->route('dashboard.transferencias.index', ['ump' => true])->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            LogErroService::registrar([
                'msg' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->route('dashboard.transferencias.index', ['ump' => true])->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }
}
