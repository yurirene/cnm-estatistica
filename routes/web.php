<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FederacaoController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\SinodalController;
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


Route::group(['middleware' => 'auth', 'prefix' => 'dashboard', 'as' => 'dashboard.'], function() {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::resource('sinodais', SinodalController::class)->parameters(['sinodais' => 'sinodal'])->except('delete')->names('sinodais');
    Route::get('/sinodais/{sinodal}/delete', [SinodalController::class, 'delete'])->name('sinodais.delete');
    
    Route::resource('federacoes', FederacaoController::class)->parameters(['federacoes' => 'federacao'])->names('federacoes')->except('delete');
    Route::get('/federacoes/{federacao}/delete', [FederacaoController::class, 'delete'])->name('federacoes.delete');

    Route::resource('umps-locais', LocalController::class)->parameters(['umps-locais' => 'local'])->names('locais')->except('delete');
    Route::get('/umps-locais/{local}/delete', [LocalController::class, 'delete'])->name('locais.delete');

});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
