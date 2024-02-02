<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\RankingDataTable;
use App\Http\Controllers\Controller;
use App\Models\Apps\App;
use App\Models\Sinodal;
use App\Services\LogErroService;
use Illuminate\Http\Request;

class AppController extends Controller
{

    public function index(RankingDataTable $dataTable)
    {
        $apps = App::get()->pluck('nome', 'id')->toArray();
        return $dataTable->render('dashboard.apps.index', [
            'apps' => $apps
        ]);
    }

    public function liberar(Request $request)
    {
        try {
            if (
                $request->filled('sinodal_id')
                && !$request->filled('sinodais')
            ) {
                $sinodais = [$request->sinodal_id];
            } else {
                $sinodais = explode(',', $request->sinodais);
            }
            foreach ($sinodais as $sinodal) {
                $sinodal = Sinodal::find($sinodal);
                $sinodal->apps()->sync($request->apps);
            }

            return redirect()->route('dashboard.apps.liberar')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'App adicionado com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'msg' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function getSinodalApps($id)
    {

        try {
            $sinodal = Sinodal::find($id);
            return response()->json(['apps' => $sinodal->apps->pluck('id')], 200);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()], 500);
        }
    }
}
