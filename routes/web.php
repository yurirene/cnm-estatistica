<?php

use App\Http\Controllers\Apps\AppController;
use App\Http\Controllers\Apps\CategoriaController;
use App\Http\Controllers\Apps\EventoController;
use App\Http\Controllers\Apps\SiteController;
use App\Http\Controllers\Apps\TesourariaController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\ColetorDadosController;
use App\Http\Controllers\ComissaoExecutivaController;
use App\Http\Controllers\ComprovanteACIController;
use App\Http\Controllers\Diretorias\DiretoriasFederacaoController;
use App\Http\Controllers\Produtos\ConsignacaoProdutoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatatableAjaxController;
use App\Http\Controllers\DetalhamentoController;
use App\Http\Controllers\DigestoController;
use App\Http\Controllers\Diretorias\DiretoriasLocalController;
use App\Http\Controllers\Diretorias\DiretoriasSinodalController;
use App\Http\Controllers\Estatistica\EstatisticaController;
use App\Http\Controllers\Produtos\EstoqueProdutoController;
use App\Http\Controllers\Instancias\FederacaoController;
use App\Http\Controllers\Formularios\FormularioFederacaoController;
use App\Http\Controllers\Formularios\FormularioLocalController;
use App\Http\Controllers\Formularios\FormularioSinodalController;
use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\Instancias\LocalController;
use App\Http\Controllers\PesquisaController;
use App\Http\Controllers\Produtos\ProdutoController;
use App\Http\Controllers\Instancias\SinodalController;
use App\Http\Controllers\Produtos\FluxoCaixaController;
use App\Http\Controllers\Produtos\PedidoController;
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



Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');
Route::get('/digesto', [DigestoController::class, 'digesto'])
    ->name('digesto');
Route::get('/digesto/exibir/{path}', [DigestoController::class, 'exibir'])
    ->name('digesto.exibir');
Route::get('/estatistica', [EstatisticaController::class, 'externo'])
    ->name('estatistica');

Route::get('/coletor-dados/login', [ColetorDadosController::class, 'login'])
    ->name('coletor-dados.login');
Route::get('/coletor-dados', [ColetorDadosController::class, 'externo'])
    ->name('coletor-dados.externo');
Route::post('/coletor-dados/responder/{id}', [ColetorDadosController::class, 'responder'])
    ->name('coletor-dados.responder');
Route::get('/coletor-dados/restaurar/{local}', [ColetorDadosController::class, 'restaurar'])
    ->name('coletor-dados.restaurar');

Route::group(['prefix' => 'site'], function () {
    Route::get('/{sigla}', [SiteController::class, 'show'])
        ->name('meusite.index');
    Route::get('/{sigla}/evento', [EventoController::class, 'show'])
        ->name('meusite.evento');
    Route::post('/{sigla}/evento/inscricao', [EventoController::class, 'inscricao'])
        ->name('meusite.evento.inscricao');
});


Route::group(['prefix' => 'graficos'], function () {
    Route::post('/', [EstatisticaController::class, 'graficos'])
        ->name('graficos.index');
});


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/home', [DashboardController::class, 'index'])
        ->name('home');
    Route::post('/trocar-senha', [DashboardController::class, 'trocarSenha'])
        ->name('trocar-senha');
});


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'usuarios'], function () {
        Route::resource('usuarios', UserController::class)
            ->names('usuarios');
        Route::post('/usuarios-senha-reset/{usuario}', [UserController::class, 'resetSenha'])
            ->name('usuarios.reset-senha');

        Route::get('/usuarios-senha-resetar/{usuario}', [UserController::class, 'resetarSenha'])
            ->name('usuarios.resetar-senha');
        Route::post('/check-usuario', [UserController::class, 'checkUser'])
            ->name('usuarios.check-usuario');
    });
});


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'sinodais'], function () {
        Route::resource('sinodais', SinodalController::class)
            ->parameters(['sinodais' => 'sinodal'])
            ->except('destroy')
            ->names('sinodais');
        Route::get('/sinodais/{sinodal}/delete', [SinodalController::class, 'delete'])
            ->name('sinodais.delete');
        Route::put('/sinodais/{sinodal}/update-info', [SinodalController::class, 'updateInfo'])
            ->name('sinodais.update-info');
        Route::get('/sinodais/get-ranking', [SinodalController::class, 'getRanking'])
            ->name('sinodais.get-ranking');
    });
});


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'federacoes'], function () {
        Route::resource('federacoes', FederacaoController::class)
            ->parameters(['federacoes' => 'federacao'])
            ->names('federacoes')->except('destroy');
        Route::get('/federacoes/{federacao}/delete', [FederacaoController::class, 'delete'])
            ->name('federacoes.delete');
        Route::put('/federacoes/{federacao}/update-info', [FederacaoController::class, 'updateInfo'])
            ->name('federacoes.update-info');
    });
});


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'umps-locais'], function () {
        Route::resource('umps-locais', LocalController::class)
            ->parameters(['umps-locais' => 'local'])
            ->names('locais')->except('destroy');
        Route::get('/umps-locais/{local}/delete', [LocalController::class, 'delete'])
            ->name('locais.delete');
        Route::put('/umps-locais/{local}/update-info', [LocalController::class, 'updateInfo'])
            ->name('locais.update-info');
    });
});

// COLETOR DE DADOS
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'coletor-dados'], function () {
        Route::get('/coletor-dados', [ColetorDadosController::class, 'index'])
            ->name('coletor-dados.index');
        Route::post('/coletor-dados', [ColetorDadosController::class, 'store'])
            ->name('coletor-dados.store');
        Route::get('/coletor-dados/delete/{id}', [ColetorDadosController::class, 'delete'])
            ->name('coletor-dados.delete');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'formularios-locais'], function () {
        Route::get('/formularios-locais', [FormularioLocalController::class, 'index'])
            ->name('formularios-locais.index');
        Route::post('/formularios-locais', [FormularioLocalController::class, 'store'])
            ->name('formularios-locais.store');
        Route::post('/formularios-locais-view', [FormularioLocalController::class, 'view'])
            ->name('formularios-locais.view');
        Route::get('/formularios-locais-export/{ano}', [FormularioLocalController::class, 'export'])
            ->name('formularios-locais.export');
        Route::get('/formularios-local-export/{local}', [FormularioLocalController::class, 'localExport'])
            ->name('formularios-local.export');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'formularios-sinodais'], function () {
        Route::get('/formularios-sinodais', [FormularioSinodalController::class, 'index'])
            ->name('formularios-sinodais.index');
        Route::post('/formularios-sinodais', [FormularioSinodalController::class, 'store'])
            ->name('formularios-sinodais.store');
        Route::post('/formularios-sinodais-view', [FormularioSinodalController::class, 'view'])
            ->name('formularios-sinodais.view');
        Route::post('/formularios-sinodais-resumo', [FormularioSinodalController::class, 'resumoTotalizador'])
            ->name('formularios-sinodais.resumo');
        Route::get('/formularios-sinodais-get-federacoes', [FormularioSinodalController::class, 'getFederacoes'])
            ->name('formularios-sinodais.get-federacoes');
        Route::get('/formularios-sinodais-export/{ano}', [FormularioSinodalController::class, 'export'])
            ->name('formularios-sinodais.export');
        Route::get('/formularios-sinodal-export/{sinodal}', [FormularioSinodalController::class, 'sinodalExport'])
            ->name('formularios-sinodal.export');
        Route::post('/formularios-sinodais-salvar', [FormularioSinodalController::class, 'salvarPreenchimento'])
            ->name('formularios-sinodais.apenas-salvar');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'formularios-federacoes'], function () {
        Route::get('/formularios-federacoes', [FormularioFederacaoController::class, 'index'])
            ->name('formularios-federacoes.index');
        Route::post('/formularios-federacoes', [FormularioFederacaoController::class, 'store'])
            ->name('formularios-federacoes.store');
        Route::post('/formularios-federacoes-view', [FormularioFederacaoController::class, 'view'])
            ->name('formularios-federacoes.view');
        Route::post('/formularios-federacoes-resumo', [FormularioFederacaoController::class, 'resumoTotalizador'])
            ->name('formularios-federacoes.resumo');
        Route::get('/formularios-federacoes-export/{ano}', [FormularioFederacaoController::class, 'export'])
            ->name('formularios-federacoes.export');
        Route::get(
            '/formularios-federacao-export/{federacao}',
            [FormularioFederacaoController::class, 'federacaoExport']
        )->name('formularios-federacao.export');
        Route::post('/formularios-federacoes-salvar', [FormularioFederacaoController::class, 'salvarPreenchimento'])
            ->name('formularios-federacoes.apenas-salvar');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'pesquisas'], function () {
        Route::resource('/pesquisas', PesquisaController::class)
            ->names('pesquisas');
        Route::get('/pesquisas/{pesquisa}/status', [PesquisaController::class, 'status'])
            ->name('pesquisas.status');
        Route::get('/pesquisas/{pesquisa}/respostas', [PesquisaController::class, 'respostas'])
            ->name('pesquisas.respostas');
        Route::post('/pesquisas-responder', [PesquisaController::class, 'responder'])
            ->name('pesquisas.responder');
        Route::get('/pesquisas/{pesquisa}/configuracoes', [PesquisaController::class, 'configuracoes'])
            ->name('pesquisas.configuracoes');
        Route::get('/pesquisas/{pesquisa}/relatorio', [PesquisaController::class, 'relatorio'])
            ->name('pesquisas.relatorio');
        Route::get('/pesquisas/{pesquisa}/limpar-respostas', [PesquisaController::class, 'limparRespostas'])
            ->name('pesquisas.limpar-respostas');
        Route::put('/pesquisas-configuracoes/{pesquisa}/update', [PesquisaController::class, 'configuracoesUpdate'])
            ->name('pesquisas.configuracoes-update');
        Route::get('/pesquisas-configuracoes/{pesquisa}/export', [PesquisaController::class, 'exportExcel'])
            ->name('pesquisas.relatorio.excel');
        Route::get('/pesquisas-acompanhar/{pesquisa}', [PesquisaController::class, 'acompanhar'])
            ->name('pesquisas.acompanhar');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'comprovante-aci'], function () {
        Route::get('/comprovante-aci', [ComprovanteACIController::class, 'index'])
            ->name('comprovante-aci.index');
        Route::post('/comprovante-aci', [ComprovanteACIController::class, 'store'])
            ->name('comprovante-aci.store');
        Route::get('/comprovante-aci/{comprovante}/status', [ComprovanteACIController::class, 'status'])
            ->name('comprovante-aci.status');
    });
});

// PAINEL ESTATISTICA
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'secretaria-estatistica'], function () {
        Route::get('/estatistica', [EstatisticaController::class, 'index'])
            ->name('estatistica.index');
        Route::post('/estatistica/atualizarParametro', [EstatisticaController::class, 'atualizarParametro'])
            ->name('estatistica.atualizarParametro');
        Route::post('/estatistica/exportarExcel', [EstatisticaController::class, 'exportarExcel'])
            ->name('estatistica.exportarExcel');
        Route::get('/estatistica/atualizar-ranking', [EstatisticaController::class, 'atualizarRanking'])
            ->name('estatistica.atualizar-ranking');
        Route::get('/estatistica/atualizar-tudo', [EstatisticaController::class, 'atualizarTodosOsDados'])
            ->name('estatistica.atualizar-tudo');
    });
});

// SECRETARIA DE PRODUTOS
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'secretaria-produtos'], function () {
        Route::resource('produtos', ProdutoController::class)
            ->parameters(['produtos' => 'produto'])
                ->names('produtos')->except('destroy');
        Route::get('/produtos/{produto}/delete', [ProdutoController::class, 'delete'])
            ->name('produtos.delete');
        Route::get('/produtos-datatable/produtos', [ProdutoController::class, 'produtoDataTable'])
            ->name('produtos.datatable.produtos');
        Route::get('/produtos-datatable/estoque', [ProdutoController::class, 'estoqueProdutosDataTable'])
            ->name('produtos.datatable.estoque');
        Route::get('/produtos-datatable/consignacao', [ProdutoController::class, 'consignacaoProdutosDataTable'])
            ->name('produtos.datatable.consignacao');

        Route::resource('estoque-produtos', EstoqueProdutoController::class)
            ->parameters(['estoque-produtos' => 'estoque'])
                ->names('estoque-produtos')->except(['index', 'show', 'destroy']);
        Route::get('/estoque-produtos/{estoque}/delete', [EstoqueProdutoController::class, 'delete'])
            ->name('estoque-produtos.delete');

        Route::resource('consignacao-produtos', ConsignacaoProdutoController::class)
            ->parameters(['consignacao-produtos' => 'consignado'])
            ->names('consignacao-produtos')
            ->except(['index', 'show', 'destroy']);
        Route::get('/consignacao-produtos/{consignado}/delete', [ConsignacaoProdutoController::class, 'delete'])
            ->name('consignacao-produtos.delete');

        Route::resource('produtos/fluxo-caixa', FluxoCaixaController::class)
            ->parameters(['fluxo-caixa' => 'fluxo'])
            ->names('produtos.fluxo-caixa')
            ->except(['index', 'show', 'destroy']);
        Route::get('/produtos/fluxo-caixa/{fluxo}/delete', [FluxoCaixaController::class, 'delete'])
            ->name('produtos.fluxo-caixa.delete');
        Route::get('/produtos-datatable/fluxo-caixa', [FluxoCaixaController::class, 'fluxoCaixaDataTable'])
            ->name('produtos.datatable.fluxo-caixa');
    });
});

// SECRETARIA DE PRODUTOS - PDV
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'pedidos'], function () {
        Route::resource('pedidos', PedidoController::class)
            ->names('pedidos')
            ->except(['create', 'show', 'edit', 'update', 'destroy']);

        Route::get('/pedidos/caixa', [PedidoController::class, 'caixa'])
            ->name('pedidos.caixa');
        Route::get('/pedidos/pagar/{pedido}/{formaPagamento}', [PedidoController::class, 'pagar'])
            ->name('pedidos.pagar');
        Route::get('/pedidos/cancelar/{pedido}', [PedidoController::class, 'cancelar'])
            ->name('pedidos.cancelar');
    });
});

//SECRETARIA EXECUTIVA
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'digestos'], function () {
        Route::resource('digestos', DigestoController::class)
            ->parameters(['digestos' => 'digesto'])
            ->names('digestos')->except('destroy');
        Route::get('/digestos/{digesto}/delete', [DigestoController::class, 'delete'])
            ->name('digestos.delete');
    });
});

Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'tutoriais'], function () {
        Route::get('/tutoriais', [TutorialController::class, 'index'])
            ->name('tutoriais.index');
    });
});

// DATATABLES
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'datatables'], function () {
        Route::get(
            '/datatables/formularios-entregues/{instancia}/{id?}',
            [DatatableAjaxController::class, 'formulariosEntregues']
        )->name('datatables.formularios-entregues');
        Route::get(
            '/datatables/informacao-federacoes/{federacao}',
            [DatatableAjaxController::class, 'informacaoFederacao']
        )->name('datatables.informacao-federacoes');
        Route::get(
            '/datatables/pesquisas/{pesquisa}/sinodais',
            [DatatableAjaxController::class, 'acompanhamentoPesquisaSinodais']
        )->name('datatables.pesquisas.sinodais');
        Route::get(
            '/datatables/pesquisas/{pesquisa}/federacoes',
            [DatatableAjaxController::class, 'acompanhamentoPesquisaFederacoes']
        )->name('datatables.pesquisas.federacoes');
        Route::get(
            '/datatables/pesquisas/{pesquisa}/locais',
            [DatatableAjaxController::class, 'acompanhamentoPesquisaLocais']
        )->name('datatables.pesquisas.locais');
        Route::get(
            '/datatables/estatistica/formularios-sinodais',
            [DatatableAjaxController::class, 'estatisticaFormulariosSinodais']
        )->name('datatables.estatistica.formularios-sinodais');
        Route::get(
            '/datatables/estatistica/formularios-locais/{id}',
            [DatatableAjaxController::class, 'estatisticaFormulariosLocais']
        )->name('datatables.estatistica.formularios-locais');
    });
});

//ACESSO APPS
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'acesso-apps'], function () {
        Route::get('/apps/liberar', [AppController::class, 'index'])
            ->name('apps.liberacao');
        Route::post('/apps/liberar', [AppController::class, 'liberar'])
            ->name('apps.liberar');
        Route::get('/apps/get-sinodal-apps/{id}', [AppController::class, 'getSinodalApps'])
            ->name('apps.get-sinodal-apps');
    });
});


// APPS
Route::group(
    [
        'middleware' => ['auth', 'auth-sistema'],
        'prefix' => 'dashboard',
        'as' => 'dashboard.'
    ],
    function () {
        Route::group(['modulo' => 'sites'], function () {
            Route::get('/apps/sites', [SiteController::class, 'index'])
                ->name('apps.sites.index');
            Route::post('/apps/sites/{sinodal_id}/atualizar-config',[SiteController::class, 'atualizar'])
                ->name('apps.sites.atualizar-config');
            Route::post('/apps/sites/{sinodal_id}/adicionar-galeria',[SiteController::class, 'adicionarGaleria'])
                ->name('apps.sites.adicionar-galeria');
            Route::get('/apps/sites/{sinodal_id}/remover-galeria/{id}',[SiteController::class, 'removerGaleria'])
                ->name('apps.sites.remover-galeria');
            Route::post(
                '/apps/sites/{sinodal_id}/atualizar-foto-diretoria',
                [SiteController::class, 'atualizarFotoDiretoria']
            )->name('apps.sites.atualizar-foto-diretoria');
            Route::post('/apps/sites/{sinodal_id}/nova-secretaria',[SiteController::class, 'novaSecretaria'])
                ->name('apps.sites.nova-secretaria');
            Route::get(
                '/apps/sites/{sinodal_id}/remover-secretaria/{config}/{chave}',
                [SiteController::class, 'removerSecretaria']
            )->name('apps.sites.remover-secretaria');
            Route::get('/apps/sites/{sinodal_id}/status',[EventoController::class, 'status'])
                ->name('apps.sites.eventos.status');
            Route::get('/apps/sites/{sinodal_id}/update',[EventoController::class, 'update'])
                ->name('apps.sites.eventos.update');
            Route::put('/apps/sites/{evento_id}/atualizar-config-evento',[EventoController::class, 'atualizar'])
                ->name('apps.sites.atualizar-config-evento');
            Route::get('/apps/sites/{evento_id}/status-evento',[EventoController::class, 'status'])
                ->name('apps.sites.status-evento');
            Route::get('/apps/sites/{evento_id}/limpar-config',[EventoController::class, 'limparConfig'])
                ->name('apps.sites.limpar-config');
            Route::get(
                '/apps/sites/{evento_id}/status-inscrito/{inscrito_id}',
                [EventoController::class, 'statusInscrito']
            )->name('apps.sites.status-inscrito');
            Route::get(
                '/apps/sites/{evento_id}/remover-inscrito/{inscrito_id}',
                [EventoController::class, 'removerInscrito']
            )->name('apps.sites.remover-inscrito');
            Route::get('/apps/sites/{evento_id}/limpar-lista',[EventoController::class, 'limparLista'])
                ->name('apps.sites.limpar-lista');
        });
    }
);

// APPS
Route::group(
    [
        'middleware' => ['auth', 'auth-sistema'],
        'prefix' => 'dashboard',
        'as' => 'dashboard.'
    ],
    function () {
        Route::group(['modulo' => 'apps'], function () {
            //TESOURARIA

            Route::resource('/apps/tesouraria', TesourariaController::class)
                ->names('apps.tesouraria')
                ->except(['destroy']);
            Route::get('/apps/tesouraria/remover/{lancamento}',[TesourariaController::class, 'delete'])
                ->name('apps.tesouraria.delete');

            Route::resource('/apps/tesouraria/categoria', CategoriaController::class)
                ->names('apps.tesouraria.categoria')
                ->except(['destroy', 'index']);

            Route::get('/apps/tesouraria/remover-categoria/{categoria}',[CategoriaController::class, 'delete'])
                ->name('apps.tesouraria.categoria.delete');

            Route::post('/apps/tesouraria/gerar-relatorio',[TesourariaController::class, 'gerarRelatorio'])
                ->name('apps.tesouraria.gerar-relatorio');
        });
    }
);

//AVISOS
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'avisos'], function () {
        Route::get('/avisos', [AvisoController::class, 'index'])
            ->name('avisos.index');
        Route::get('/listar-avisos', [AvisoController::class, 'listar'])
            ->name('avisos.listar');
        Route::post('/avisos', [AvisoController::class, 'store'])
            ->name('avisos.store');
        Route::get('/avisos/visualizado/{id}', [AvisoController::class, 'visualizado'])
            ->name('avisos.visualizado');
        Route::get('/avisos/delete/{id}', [AvisoController::class, 'delete'])
            ->name('avisos.delete');
        Route::get('/avisos/get-usuarios', [AvisoController::class, 'getUsuarios'])
            ->name('avisos.get-usuarios');
        Route::get('/avisos/lista-visualizados/{id}', [AvisoController::class, 'listarVisualizados'])
            ->name('avisos.listar-visualizados');
    });
});

//DETALHAMENTO
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'detalhamento'], function () {
        Route::get('/detalhamento/{tipo}', [DetalhamentoController::class, 'index'])
            ->name('detalhamento.index');
    });
});

//DIRETORIA
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'diretoria-sinodal'], function () {
        Route::get('/diretoria-sinodal', [DiretoriasSinodalController::class, 'index'])->name('diretoria-sinodal.index');
        Route::put('/diretoria-sinodal/{diretoria}/update', [DiretoriasSinodalController::class, 'update'])->name('diretoria-sinodal.update');
    });

    Route::group(['modulo' => 'diretoria-federacao'], function () {
        Route::get('/diretoria-federacao', [DiretoriasFederacaoController::class, 'index'])->name('diretoria-federacao.index');
        Route::put('/diretoria-federacao/{diretoria}/update', [DiretoriasFederacaoController::class, 'update'])->name('diretoria-federacao.update');
    });

    Route::group(['modulo' => 'diretoria-local'], function () {
        Route::get('/diretoria-local', [DiretoriasLocalController::class, 'index'])->name('diretoria-local.index');
        Route::put('/diretoria-local/{diretoria}/update', [DiretoriasLocalController::class, 'update'])->name('diretoria-local.update');
    });
});



//HELPDESK
Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::group(['modulo' => 'helpdesk'], function () {
        Route::resource('/helpdesk', HelpdeskController::class)
            ->names('helpdesk')
            ->except(['update', 'delete']);
    });
});

//COMISSAO EXECUTIVA
Route::group(
    [
        'middleware' => ['auth', 'auth-sistema'],
        'prefix' => 'dashboard',
        'as' => 'dashboard.'
    ],
    function () {
        Route::group(['modulo' => 'comissao-executiva'], function () {
            Route::resource('comissao-executiva', ComissaoExecutivaController::class)
                ->names('comissao-executiva')
                ->parameter('comissao-executiva', 'reuniao')
                ->except(['destroy']);
            Route::get('comissao-executiva/{reuniao}/delete', [ComissaoExecutivaController::class, 'delete'])
                ->name('comissao-executiva.delete');
            Route::get('comissao-executiva/{reuniao}/encerrar', [ComissaoExecutivaController::class, 'encerrar'])
                ->name('comissao-executiva.encerrar');
            Route::get('comissao-executiva-credenciais-datatable', [ComissaoExecutivaController::class, 'credenciaisDatatable'])
                ->name('comissao-executiva.credenciais-datatable');

            Route::get('comissao-executiva/{documento}/confirmar', [ComissaoExecutivaController::class, 'confirmarDocumento'])
                ->name('comissao-executiva.confirmar');
        });
    }
);
//COMISSAO EXECUTIVA - ACESSO SINODAL
Route::group(
    [
        'middleware' => ['auth', 'auth-sistema'],
        'prefix' => 'dashboard',
        'as' => 'dashboard.'
    ],
    function () {

        Route::group(
            ['modulo' => 'ce-sinodal'],
            function () {
                Route::get('ce/sinodal', [ComissaoExecutivaController::class, 'sinodal'])
                    ->name('ce-sinodal.index');
                Route::post('ce/enviar-documentos', [ComissaoExecutivaController::class, 'enviarDocumento'])
                    ->name('ce-sinodal.enviar-documento');
                Route::get('ce/remover-documentos/{documento}', [ComissaoExecutivaController::class, 'removerDocumento'])
                    ->name('ce-sinodal.remover-documento');
            }
        );
    }
);
