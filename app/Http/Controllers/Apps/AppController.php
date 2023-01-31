<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\RankingDataTable;
use App\Http\Controllers\Controller;
use App\Models\Apps\App;
use App\Models\Sinodal;
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
            $sinodal = Sinodal::find($request->sinodal_id);
            $sinodal->apps()->sync($request->apps);

            return redirect()->route('dashboard.apps.liberar')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'App adicionado com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
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
