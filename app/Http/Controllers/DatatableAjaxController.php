<?php

namespace App\Http\Controllers;

use App\Models\Federacao;
use App\Models\LogErro;
use App\Models\Pesquisas\Pesquisa;
use App\Services\DatatableAjaxService;
use App\Services\LogErroService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DatatableAjaxController extends Controller
{
    public function logErros()
    {
        return DatatableAjaxService::logErros();
    }

    public function informacaoFederacao(Request $request, Federacao $federacao)
    {
        return DatatableAjaxService::informacaoFederacao($federacao);
    }

    public function acompanhamentoPesquisaSinodais(Request $request, Pesquisa $pesquisa)
    {
        return DatatableAjaxService::acompanhamentoPesquisaSinodais($pesquisa);
    }

    public function acompanhamentoPesquisaFederacoes(Request $request, Pesquisa $pesquisa)
    {
        return DatatableAjaxService::acompanhamentoPesquisaFederacoes($pesquisa);
    }

    public function acompanhamentoPesquisaLocais(Request $request, Pesquisa $pesquisa)
    {
        return DatatableAjaxService::acompanhamentoPesquisaLocais($pesquisa);
    }

    public function formulariosEntregues(string $instancia, string $id = null)
    {
        return DatatableAjaxService::formulariosEntregues($instancia, $id);
    }

    public function estatisticaFormulariosSinodais ()
    {
        return DatatableAjaxService::getFormularioSinodais();
    }

    public function estatisticaFormulariosLocais (string $id)
    {
        return DatatableAjaxService::estatisticaFormulariosLocais($id);
    }
}
