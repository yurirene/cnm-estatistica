<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Congresso\CongressoNacionalController;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function congresso()
    {
        $congressoController = app(CongressoNacionalController::class);
        $totalizador = $congressoController->getTotalizadorQuorum();
        $listaSinodaisComFederacoes = $congressoController->getSinodaisComFederacoesQuorum();

        return view('congresso', [
            'totalizador' => $totalizador,
            'listaSinodaisComFederacoes' => $listaSinodaisComFederacoes
        ]);
    }
}
