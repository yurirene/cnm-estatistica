<?php

namespace App\Http\Controllers\Diretorias;

use App\Http\Controllers\Controller;
use App\Services\Instancias\DiretoriaService;
use Illuminate\Http\Request;

class DiretoriasSinodalController extends Controller
{
    public function index()
    {
        return view('dashboard.diretoria.index', [
            'tipo' => 'Sinodal',
            'cargos' => DiretoriaService::getDiretoria()
        ]);
    }
}
