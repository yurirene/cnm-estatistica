<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Congresso\CongressoNacionalController;
use App\Models\CongressoReuniao;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function congresso()
    {
        $reuniao = CongressoReuniao::aberta()->first();
        $congressoController = app(CongressoNacionalController::class);
        $totalizador = $congressoController->getTotalizadorQuorum($reuniao?->id);
        $listaSinodaisComFederacoes = $congressoController->getSinodaisComFederacoesQuorum($reuniao?->id);

        return view('congresso', [
            'totalizador' => $totalizador,
            'listaSinodaisComFederacoes' => $listaSinodaisComFederacoes
        ]);
    }
}
