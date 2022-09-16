<?php

namespace App\Http\Controllers;

use App\DataTables\MinhasDemandasDataTable;
use App\Services\DemandasService;
use Illuminate\Http\Request;
use Throwable;

class MinhasDemandasController extends Controller
{

    public function index(MinhasDemandasDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.minhas-demandas.index', [
                'demandas' => DemandasService::getDemandas(),
                'status' => DemandasService::getStatus(),
                'niveis' => DemandasService::getNiveis(),
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
}
