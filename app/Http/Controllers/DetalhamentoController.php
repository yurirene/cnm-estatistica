<?php

namespace App\Http\Controllers;

use App\DataTables\Detalhamento\PresbiterioDatatable;
use App\Factories\DetalhamentoFactory;
use Illuminate\Http\Request;

class DetalhamentoController extends Controller
{
    public function index(string $tipo)
    {
        $dataTable = DetalhamentoFactory::make($tipo);
        return $dataTable->render('dashboard.detalhamento.index', [
            'titulo' => $dataTable->titulo,
            'subtitulo' => $dataTable->subtitulo,
            'filtros' => $dataTable->filtros()
        ]);
    }
}
