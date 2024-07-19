<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Site\Evento;
use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use App\Models\Sinodal;
use App\Services\Apps\EventoService;
use App\Services\Apps\SiteService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sinodal = auth()->user()->sinodais->first();
        $site = Site::where('sinodal_id', $sinodal->id)->first();
        $evento = Evento::where('sinodal_id', $sinodal->id)->first();
        if (!$site) {
            $site = SiteService::criarSite($sinodal);
        }
        if (!$evento) {
            $evento = EventoService::criarEvento($sinodal);
        }
        return view('dashboard.apps.sites.index', [
            'modelo' => $site->modelo,
            'site' => $site,
            'sinodal_id' => $sinodal->id,
            'evento' => $evento,
            'inscritos' => EventoService::dataTableInscritos($evento->id)
        ]);
    }

    public function atualizar(string $sinodal_id, Request $request)
    {
        try {
            SiteService::atualizarConfig($sinodal_id, $request->all());
            return response()->json([
                'mensagem' => 'Informação atualizada com Sucesso!'
            ], 200);
        } catch (\Throwable $th) {


            return response()->json([
                'mensagem' => 'Erro ao atualizar!',
                'mensagem_erro' => $th->getMessage()
            ], 500);
        }
    }

    public function show($sigla)
    {
        try {
            $sinodal = Sinodal::where('sigla', $sigla)
                ->whereHas('apps', function ($sql) {
                    $sql->where('name', 'sites');
                })->first();
            if (!$sinodal) {
                return abort(410, 'A sinodal não encontrada ou não tem essa funcionalidade habilitada');
            }
            $site = $sinodal->site;
            $variaveis = SiteService::montar($sinodal, $site->configuracoes);
            return view("sites.modelo_{$site->modelo_id}", $variaveis);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function adicionarGaleria(string $sinodal_id, Request $request)
    {
        try {
            SiteService::adicionarNaGaleria($sinodal_id, $request);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Imagem adicionada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function removerGaleria(string $sinodal_id, int $id)
    {
        try {
            SiteService::removerDaGaleria($sinodal_id, $id);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Imagem removida com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function atualizarFotoDiretoria(string $sinodal_id, Request $request)
    {
        try {
            SiteService::atualizarFotoDiretoria($sinodal_id, $request);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Imagem alterada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function novaSecretaria(string $sinodal_id, Request $request)
    {
        try {
            SiteService::novaSecretaria($sinodal_id, $request);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Secretaria alterada com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }


    public function removerSecretaria(string $sinodal_id, int $config, int $chave)
    {
        try {
            SiteService::removerSecretaria($sinodal_id, $config, $chave);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Imagem removida com Sucesso!'
                ]
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }
}
