<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Site\EventoInscrito;
use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use App\Models\Sinodal;
use App\Services\Apps\EventoService;
use App\Services\Apps\SiteService;
use Exception;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    /**
     * Método para atualizar as configurações do evento
     *
     * @param string $evento_id
     * @param Request $request
     * @return void
     */
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

    /**
     * Método responsável por renderizar a página de evento
     *
     * @param [type] $sigla
     * @return void
     */
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

    /**
     * Método para persistir a inscrição no banco de dados
     *
     * @param string $sigla
     * @param Request $request
     * @return void
     */
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
                'status' => true,
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('meusite.evento', $sigla)->with([
                'status' => false,
            ]);
        }
    }

    /**
     * Método para inverter o status do evento
     *
     * @param string $evento_id
     * @return void
     */
    public function status(string $evento_id)
    {
        try {
            EventoService::updateStatus($evento_id);
            return response()->json([
                'mensagem' => 'Status atualizado com Sucesso!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensagem' => 'Erro ao atualizar!',
                'mensagem_erro' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Método para limpar config
     *
     * @param string $evento_id
     * @return void
     */
    public function limparConfig(string $evento_id)
    {
        try {
            EventoService::limparConfig($evento_id);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
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

    /**
     * Altera o status do inscrito
     *
     * @param string $evento_id
     * @param integer $inscrito_id
     * @return void
     */
    public function statusInscrito(string $evento_id, int $inscrito_id)
    {
        try {
            $retorno = EventoService::statusInscrito($evento_id, $inscrito_id);
            return response()->json([
                'mensagem' => 'Status atualizado com Sucesso!',
                'status' => $retorno
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'mensagem' => 'Erro ao atualizar!',
                'mensagem_erro' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remover o inscrito
     *
     * @param string $evento_id
     * @param integer $inscrito_id
     * @return void
     */
    public function removerInscrito(string $evento_id, int $inscrito_id)
    {
        try {
            EventoService::removerInscrito($evento_id, $inscrito_id);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Inscrito removido com Sucesso!'
                ],
                'aba' => 'inscritos'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Erro ao remover o Inscrito!'
                ],
                'aba' => 'inscritos'
            ]);
        }
    }

    /**
     * Limpar a lista de inscritos
     * removendo toda a lista do evento
     *
     * @param string $evento_id
     * @param integer $inscrito_id
     * @return void
     */
    public function limparLista(string $evento_id)
    {
        try {
            EventoService::limparLista($evento_id);
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com sucesso!'
                ],
                'aba' => 'inscritos'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.apps.sites.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Erro ao realizar operação!'
                ],
                'aba' => 'inscritos'
            ]);
        }
    }


}
