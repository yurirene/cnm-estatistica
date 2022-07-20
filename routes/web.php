<?php

use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\ComprovanteACIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatatableAjaxController;
use App\Http\Controllers\FederacaoController;
use App\Http\Controllers\Formularios\FormularioFederacaoController;
use App\Http\Controllers\Formularios\FormularioLocalController;
use App\Http\Controllers\Formularios\FormularioSinodalController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\SinodalController;
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


Route::group(['middleware' => ['auth', 'auth-sistema'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function() {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::post('/trocar-senha', [DashboardController::class, 'trocarSenha'])->name('trocar-senha');

    Route::resource('usuarios', UserController::class)->names('usuarios');

    Route::resource('sinodais', SinodalController::class)->parameters(['sinodais' => 'sinodal'])->except('delete')->names('sinodais');
    Route::get('/sinodais/{sinodal}/delete', [SinodalController::class, 'delete'])->name('sinodais.delete');
    Route::put('/sinodais/{sinodal}/update-info', [SinodalController::class, 'updateInfo'])->name('sinodais.update-info');
    
    Route::resource('federacoes', FederacaoController::class)->parameters(['federacoes' => 'federacao'])->names('federacoes')->except('delete');
    Route::get('/federacoes/{federacao}/delete', [FederacaoController::class, 'delete'])->name('federacoes.delete');
    Route::put('/federacoes/{federacao}/update-info', [FederacaoController::class, 'updateInfo'])->name('federacoes.update-info');

    Route::resource('umps-locais', LocalController::class)->parameters(['umps-locais' => 'local'])->names('locais')->except('delete');
    Route::get('/umps-locais/{local}/delete', [LocalController::class, 'delete'])->name('locais.delete');

    Route::resource('atividades', AtividadeController::class)->parameters(['atividades' => 'atividade'])->names('atividades')->except('delete');
    Route::get('/atividades/{atividade}/delete', [AtividadeController::class, 'delete'])->name('atividades.delete');
    Route::get('/atividades-calendario', [AtividadeController::class, 'calendario'])->name('atividades.calendario');
    Route::get('/atividades/{atividade}/confirmar', [AtividadeController::class, 'confirmar'])->name('atividades.confirmar');

    Route::get('/formularios-locais', [FormularioLocalController::class, 'index'])->name('formularios-locais.index');
    Route::post('/formularios-locais', [FormularioLocalController::class, 'store'])->name('formularios-locais.store');
    Route::post('/formularios-locais-view', [FormularioLocalController::class, 'view'])->name('formularios-locais.view');
    
    Route::get('/formularios-sinodais', [FormularioSinodalController::class, 'index'])->name('formularios-sinodais.index');
    Route::post('/formularios-sinodais', [FormularioSinodalController::class, 'store'])->name('formularios-sinodais.store');
    Route::post('/formularios-sinodais-view', [FormularioSinodalController::class, 'view'])->name('formularios-sinodais.view');
    Route::post('/formularios-sinodais-resumo', [FormularioSinodalController::class, 'resumoTotalizador'])->name('formularios-sinodais.resumo');
    Route::post('/formularios-sinodais-importar-validar', [FormularioSinodalController::class, 'validarImportacao'])->name('formularios-sinodais.importar-validar');
    Route::post('/formularios-sinodais-importar', [FormularioSinodalController::class, 'importar'])->name('formularios-sinodais.importar');
    Route::get('/formularios-sinodais-get-federacoes', [FormularioSinodalController::class, 'getFederacoes'])->name('formularios-sinodais.get-federacoes');

    Route::get('/formularios-federacoes', [FormularioFederacaoController::class, 'index'])->name('formularios-federacoes.index');
    Route::post('/formularios-federacoes', [FormularioFederacaoController::class, 'store'])->name('formularios-federacoes.store');
    Route::post('/formularios-federacoes-view', [FormularioFederacaoController::class, 'view'])->name('formularios-federacoes.view');
    Route::post('/formularios-federacoes-resumo', [FormularioFederacaoController::class, 'resumoTotalizador'])->name('formularios-federacoes.resumo');

    Route::get('/listar-formularios-federacoes/{sinodal}', [DashboardController::class, 'listarFormulariosFederacoes'])->name('formulario-federacoes.list');
    Route::get('/listar-formularios-locais/{federacao}', [DashboardController::class, 'listarFormulariosLocais'])->name('formulario-locais.list');


    Route::get('/comprovante-aci', [ComprovanteACIController::class, 'index'])->name('comprovante-aci.index');
    Route::post('/comprovante-aci', [ComprovanteACIController::class, 'store'])->name('comprovante-aci.store');
    Route::get('/comprovante-aci/{comprovante}/status', [ComprovanteACIController::class, 'status'])->name('comprovante-aci.status');

    Route::get('/datatables/log-erro', [DatatableAjaxController::class, 'logErros'])->name('datatables.log-erros');

});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
