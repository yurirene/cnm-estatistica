<?php

use App\Http\Controllers\Apps\AppController;
use App\Http\Controllers\Apps\SiteController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\ComprovanteACIController;
use App\Http\Controllers\ConsignacaoProdutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatatableAjaxController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\DigestoController;
use App\Http\Controllers\EstatisticaController;
use App\Http\Controllers\EstoqueProdutoController;
use App\Http\Controllers\FederacaoController;
use App\Http\Controllers\Formularios\FormularioFederacaoController;
use App\Http\Controllers\Formularios\FormularioLocalController;
use App\Http\Controllers\Formularios\FormularioSinodalController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\MinhasDemandasController;
use App\Http\Controllers\PesquisaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SinodalController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['register' => false]);

Route::group(['prefix' => 'site'], function() {
    Route::get('/{sigla}', [SiteController::class, 'show'])->name('meusite.index');
});

Route::group(['prefix' => 'graficos'], function() {
    Route::post('/', [EstatisticaController::class, 'graficos'])->name('graficos.index');
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function() {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::post('/trocar-senha', [DashboardController::class, 'trocarSenha'])->name('trocar-senha');

    Route::group(['modulo' => 'usuarios'], function () {
        Route::resource('usuarios', UserController::class)->names('usuarios');
        Route::post('/usuarios-senha-reset/{usuario}', [UserController::class, 'resetSenha'])->name('usuarios.reset-senha');
        Route::post('/check-usuario', [UserController::class, 'checkUser'])->name('usuarios.check-usuario');
    });

    Route::group(['modulo' => 'sinodais'], function() {
        Route::resource('sinodais', SinodalController::class)->parameters(['sinodais' => 'sinodal'])->except('delete')->names('sinodais');
        Route::get('/sinodais/{sinodal}/delete', [SinodalController::class, 'delete'])->name('sinodais.delete');
        Route::put('/sinodais/{sinodal}/update-info', [SinodalController::class, 'updateInfo'])->name('sinodais.update-info');
        Route::get('/sinodais/get-ranking', [SinodalController::class, 'getRanking'])->name('sinodais.get-ranking');
    });
    Route::group(['modulo' => 'federacoes'], function() {
        Route::resource('federacoes', FederacaoController::class)->parameters(['federacoes' => 'federacao'])->names('federacoes')->except('delete');
        Route::get('/federacoes/{federacao}/delete', [FederacaoController::class, 'delete'])->name('federacoes.delete');
        Route::put('/federacoes/{federacao}/update-info', [FederacaoController::class, 'updateInfo'])->name('federacoes.update-info');
    });
    Route::group(['modulo' => 'umps-locais'], function() {
        Route::resource('umps-locais', LocalController::class)->parameters(['umps-locais' => 'local'])->names('locais')->except('delete');
        Route::get('/umps-locais/{local}/delete', [LocalController::class, 'delete'])->name('locais.delete');
        Route::put('/umps-locais/{local}/update-info', [LocalController::class, 'updateInfo'])->name('locais.update-info');
    });
    Route::group(['modulo' => 'atividades'], function() {
        Route::resource('atividades', AtividadeController::class)->parameters(['atividades' => 'atividade'])->names('atividades')->except('delete');
        Route::get('/atividades/{atividade}/delete', [AtividadeController::class, 'delete'])->name('atividades.delete');
        Route::get('/atividades-calendario', [AtividadeController::class, 'calendario'])->name('atividades.calendario');
        Route::get('/atividades/{atividade}/confirmar', [AtividadeController::class, 'confirmar'])->name('atividades.confirmar');
    });
    Route::group(['modulo' => 'formularios-locais'], function() {
        Route::get('/formularios-locais', [FormularioLocalController::class, 'index'])->name('formularios-locais.index');
        Route::post('/formularios-locais', [FormularioLocalController::class, 'store'])->name('formularios-locais.store');
        Route::post('/formularios-locais-view', [FormularioLocalController::class, 'view'])->name('formularios-locais.view');
        Route::get('/formularios-locais-export/{ano}', [FormularioLocalController::class, 'export'])->name('formularios-locais.export');
        Route::get('/formularios-local-export/{local}', [FormularioLocalController::class, 'localExport'])->name('formularios-local.export');

    });

    Route::group(['modulo' => 'formularios-sinodais'], function() {
        Route::get('/formularios-sinodais', [FormularioSinodalController::class, 'index'])->name('formularios-sinodais.index');
        Route::post('/formularios-sinodais', [FormularioSinodalController::class, 'store'])->name('formularios-sinodais.store');
        Route::post('/formularios-sinodais-view', [FormularioSinodalController::class, 'view'])->name('formularios-sinodais.view');
        Route::post('/formularios-sinodais-resumo', [FormularioSinodalController::class, 'resumoTotalizador'])->name('formularios-sinodais.resumo');
        Route::get('/formularios-sinodais-get-federacoes', [FormularioSinodalController::class, 'getFederacoes'])->name('formularios-sinodais.get-federacoes');

    });

    Route::group(['modulo' => 'formularios-federacoes'], function() {
        Route::get('/formularios-federacoes', [FormularioFederacaoController::class, 'index'])->name('formularios-federacoes.index');
        Route::post('/formularios-federacoes', [FormularioFederacaoController::class, 'store'])->name('formularios-federacoes.store');
        Route::post('/formularios-federacoes-view', [FormularioFederacaoController::class, 'view'])->name('formularios-federacoes.view');
        Route::post('/formularios-federacoes-resumo', [FormularioFederacaoController::class, 'resumoTotalizador'])->name('formularios-federacoes.resumo');
        Route::get('/formularios-federacoes-export/{ano}', [FormularioFederacaoController::class, 'export'])->name('formularios-federacoes.export');
        Route::get('/formularios-federacao-export/{federacao}', [FormularioFederacaoController::class, 'federacaoExport'])->name('formularios-federacao.export');
    });

    Route::group(['modulo' => 'pesquisas'], function() {
        Route::resource('/pesquisas', PesquisaController::class)->names('pesquisas');
        Route::get('/pesquisas/{pesquisa}/status', [PesquisaController::class, 'status'])->name('pesquisas.status');
        Route::get('/pesquisas/{pesquisa}/respostas', [PesquisaController::class, 'respostas'])->name('pesquisas.respostas');
        Route::post('/pesquisas-responder', [PesquisaController::class, 'responder'])->name('pesquisas.responder');
        Route::get('/pesquisas/{pesquisa}/configuracoes', [PesquisaController::class, 'configuracoes'])->name('pesquisas.configuracoes');
        Route::get('/pesquisas/{pesquisa}/relatorio', [PesquisaController::class, 'relatorio'])->name('pesquisas.relatorio');
        Route::get('/pesquisas/{pesquisa}/limpar-respostas', [PesquisaController::class, 'limparRespostas'])->name('pesquisas.limpar-respostas');
        Route::put('/pesquisas-configuracoes/{pesquisa}/update', [PesquisaController::class, 'configuracoesUpdate'])->name('pesquisas.configuracoes-update');
        Route::get('/pesquisas-configuracoes/{pesquisa}/export', [PesquisaController::class, 'exportExcel'])->name('pesquisas.relatorio.excel');
        Route::get('/pesquisas-acompanhar/{pesquisa}', [PesquisaController::class, 'acompanhar'])->name('pesquisas.acompanhar');
    });

    Route::group(['modulo' => 'comprovante-aci'], function() {
        Route::get('/comprovante-aci', [ComprovanteACIController::class, 'index'])->name('comprovante-aci.index');
        Route::post('/comprovante-aci', [ComprovanteACIController::class, 'store'])->name('comprovante-aci.store');
        Route::get('/comprovante-aci/{comprovante}/status', [ComprovanteACIController::class, 'status'])->name('comprovante-aci.status');
    });

    // PAINEL ESTATISTICA
    Route::group(['modulo' => 'secretaria-estatistica'], function() {
        Route::get('/estatistica', [EstatisticaController::class, 'index'])->name('estatistica.index');
        Route::post('/estatistica/atualizarParametro', [EstatisticaController::class, 'atualizarParametro'])->name('estatistica.atualizarParametro');
        Route::post('/estatistica/exportarExcel', [EstatisticaController::class, 'exportarExcel'])->name('estatistica.exportarExcel');
        Route::get('/estatistica/atualizar-ranking', [EstatisticaController::class, 'atualizarRanking'])->name('estatistica.atualizar-ranking');
    });
    // SECRETARIA DE PRODUTOS
    Route::group(['modulo' => 'secretaria-produtos'], function() {
        Route::resource('produtos', ProdutoController::class)->parameters(['produtos' => 'produto'])->names('produtos')->except('delete');
        Route::get('/produtos/{produto}/delete', [ProdutoController::class, 'delete'])->name('produtos.delete');
        Route::get('/produtos-datatable/produtos', [ProdutoController::class, 'produtoDataTable'])->name('produtos.datatable.produtos');
        Route::get('/produtos-datatable/estoque', [ProdutoController::class, 'estoqueProdutosDataTable'])->name('produtos.datatable.estoque');
        Route::get('/produtos-datatable/consignacao', [ProdutoController::class, 'consignacaoProdutosDataTable'])->name('produtos.datatable.consignacao');

        Route::resource('estoque-produtos', EstoqueProdutoController::class)->parameters(['estoque-produtos' => 'estoque'])->names('estoque-produtos')->except(['index', 'show', 'delete']);
        Route::get('/estoque-produtos/{estoque}/delete', [EstoqueProdutoController::class, 'delete'])->name('estoque-produtos.delete');

        Route::resource('consignacao-produtos', ConsignacaoProdutoController::class)->parameters(['consignacao-produtos' => 'consignado'])->names('consignacao-produtos')->except(['index', 'show', 'delete']);
        Route::get('/consignacao-produtos/{consignado}/delete', [ConsignacaoProdutoController::class, 'delete'])->name('consignacao-produtos.delete');

    });
    // MINHAS DEMANDAS
    Route::group(['modulo' => 'minhas-demandas'], function() {
        Route::get('minhas-demandas', [MinhasDemandasController::class, 'index'])->name('minhas-demandas.index');
    });

    //SECRETARIA EXECUTIVA

    Route::group(['modulo' => 'demandas'], function() {
    Route::resource('demandas', DemandaController::class)->parameters(['demandas' => 'demanda'])->names('demandas')->except('delete');
        Route::get('/demandas/{demanda}/delete', [DemandaController::class, 'delete'])->name('demandas.delete');
        Route::get('/demandas/{demanda}/lista', [DemandaController::class, 'lista'])->name('demandas.lista');
        Route::get('/demandas/{demanda}/{item}/delete', [DemandaController::class, 'deleteItem'])->name('demandas.delete-item');
        Route::post('/demandas/{demanda}/{item}/atualizar', [DemandaController::class, 'atualizarItem'])->name('demandas.update-item');
        Route::post('/demandas/{demanda}/store-item', [DemandaController::class, 'storeItem'])->name('demandas.store-item');
        Route::post('/demandas/informacoes-adicionais', [DemandaController::class, 'informacoesAdicionais'])->name('demandas.informacoesAdicionais');
    });
    Route::group(['modulo' => 'digestos'], function() {
        Route::resource('digestos', DigestoController::class)->parameters(['digestos' => 'digesto'])->names('digestos')->except('delete');
        Route::get('/digestos/{digesto}/delete', [DigestoController::class, 'delete'])->name('digestos.delete');
    });


    Route::group(['modulo' => 'tutoriais'], function() {
        Route::get('/tutoriais', [TutorialController::class, 'index'])->name('tutoriais.index');
    });

    // DATATABLES
    Route::group(['modulo' => 'datatables'], function() {
        Route::get('/datatables/log-erro', [DatatableAjaxController::class, 'logErros'])->name('datatables.log-erros');
        Route::get('/datatables/formularios-entregues/{instancia}/{id?}', [DatatableAjaxController::class, 'formulariosEntregues'])->name('datatables.formularios-entregues');
        Route::get('/datatables/informacao-federacoes/{federacao}', [DatatableAjaxController::class, 'informacaoFederacao'])->name('datatables.informacao-federacoes');
        Route::get('/datatables/pesquisas/{pesquisa}/sinodais', [DatatableAjaxController::class, 'acompanhamentoPesquisaSinodais'])->name('datatables.pesquisas.sinodais');
        Route::get('/datatables/pesquisas/{pesquisa}/federacoes', [DatatableAjaxController::class, 'acompanhamentoPesquisaFederacoes'])->name('datatables.pesquisas.federacoes');
        Route::get('/datatables/pesquisas/{pesquisa}/locais', [DatatableAjaxController::class, 'acompanhamentoPesquisaLocais'])->name('datatables.pesquisas.locais');


        Route::get('/datatables/estatistica/formularios-sinodais', [DatatableAjaxController::class, 'estatisticaFormulariosSinodais'])->name('datatables.estatistica.formularios-sinodais');
        Route::get('/datatables/estatistica/formularios-locais/{id}', [DatatableAjaxController::class, 'estatisticaFormulariosLocais'])->name('datatables.estatistica.formularios-locais');
    });


    //ACESSO APPS
    Route::group(['modulo' => 'acesso-apps'], function () {
        Route::get('/apps/liberar', [AppController::class, 'index'])->name('apps.liberacao');
        Route::post('/apps/liberar', [AppController::class, 'liberar'])->name('apps.liberar');
        Route::get('/apps/get-sinodal-apps/{id}', [AppController::class, 'getSinodalApps'])->name('apps.get-sinodal-apps');
    });


    // APPS
    Route::group(['modulo' => 'apps'], function () {
        Route::get('/apps/sites', [SiteController::class, 'index'])->name('apps.sites.index');
        Route::post('/apps/sites/{sinodal_id}/atualizar-config',[SiteController::class, 'atualizar'])->name('apps.sites.atualizar-config');
        Route::post('/apps/sites/{sinodal_id}/adicionar-galeria',[SiteController::class, 'adicionarGaleria'])->name('apps.sites.adicionar-galeria');
        Route::get('/apps/sites/{sinodal_id}/remover-galeria/{id}',[SiteController::class, 'removerGaleria'])->name('apps.sites.remover-galeria');
        Route::post('/apps/sites/{sinodal_id}/atualizar-foto-diretoria',[SiteController::class, 'atualizarFotoDiretoria'])->name('apps.sites.atualizar-foto-diretoria');
    });


    //AVISOS
    Route::group(['modulo' => 'avisos'], function () {
        Route::get('/avisos', [AvisoController::class, 'index'])->name('avisos.index');
        Route::get('/listar-avisos', [AvisoController::class, 'listar'])->name('avisos.listar');
        Route::post('/avisos', [AvisoController::class, 'store'])->name('avisos.store');
        Route::get('/avisos/visualizado/{id}', [AvisoController::class, 'visualizado'])->name('avisos.visualizado');
        Route::get('/avisos/delete/{id}', [AvisoController::class, 'delete'])->name('avisos.delete');
        Route::get('/avisos/get-usuarios', [AvisoController::class, 'getUsuarios'])->name('avisos.get-usuarios');
    });

});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/digesto', [DigestoController::class, 'digesto'])->name('digesto');
