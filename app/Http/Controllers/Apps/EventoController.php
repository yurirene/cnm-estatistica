<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use App\Models\Sinodal;
use App\Services\Apps\EventoService;
use App\Services\Apps\SiteService;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function atualizar(string $evento_id, Request $request)
    {
        try {
            EventoService::atualizarConfig($evento_id, $request->all());
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Configurações atualizadas com Sucesso!'
                ],
                'aba' => 'evento'
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ],
                "aba" => 'evento'
            ]);
        }
    }


    public function show($sigla)
    {
        try {
            $sinodal = Sinodal::where('sigla', $sigla)
                ->whereHas('apps', function ($sql) {
                    $sql->where('name', 'sites');
                })->first();
            $evento = $sinodal->evento;
            return view("sites.modelo_evento", [
                'evento' => $evento,
                'sigla' => $sinodal->sigla
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function inscricao(string $sigla, Request $request)
    {
        try {
            $sinodal = Sinodal::where('sigla', $sigla)
                ->whereHas('apps', function ($sql) {
                    $sql->where('name', 'sites');
                })->first();
            $evento = $sinodal->evento;
            EventoService::inscricao($evento, $request->all());
            return redirect()->route('meusite.evento', $sigla)->with([
                'mensagem' => [
                    'status' => true
                ],
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('meusite.evento', $sigla)->with([
                'mensagem' => [
                    'status' => false
                ],
            ]);
        }
    }

}
