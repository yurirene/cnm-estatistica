<?php

namespace App\Http\Controllers;

use App\DataTables\MissionariosDataTable;
use App\Models\Missionario;
use App\Models\Regiao;
use App\Services\AdoteMissionarioService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdoteMissionarioController extends Controller
{
    public function index(MissionariosDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.adote-missionario.index', [
                'regioes' => AdoteMissionarioService::getRegioes(),
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function create()
    {
        try {
            return view('dashboard.adote-missionario.form', [
                'regioes' => AdoteMissionarioService::getRegioes(),
                'estados' => AdoteMissionarioService::getEstados(),
                'estadosPorRegiao' => AdoteMissionarioService::getEstadosPorRegiao(),
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            AdoteMissionarioService::store($request);

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Missionário cadastrado com sucesso!',
                ],
            ]);
        } catch (ValidationException $th) {
            throw $th;
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ])->withInput();
        }
    }

    public function edit(Missionario $missionario)
    {
        try {
            $missionario->load(['estado', 'regiao', 'sinodal', 'federacao']);

            return view('dashboard.adote-missionario.form', [
                'missionario' => $missionario,
                'regioes' => AdoteMissionarioService::getRegioes(),
                'estados' => AdoteMissionarioService::getEstados($missionario->regiao_id),
                'estadosPorRegiao' => AdoteMissionarioService::getEstadosPorRegiao(),
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function update(Missionario $missionario, Request $request)
    {
        try {
            AdoteMissionarioService::update($missionario, $request);

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Missionário atualizado com sucesso!',
                ],
            ]);
        } catch (ValidationException $th) {
            throw $th;
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ])->withInput();
        }
    }

    public function delete(Missionario $missionario)
    {
        try {
            AdoteMissionarioService::delete($missionario);

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Missionário removido com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function desfazer(Missionario $missionario)
    {
        try {
            AdoteMissionarioService::desfazerAdocao($missionario);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Adoção desfeita com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function escolher(Request $request)
    {
        try {
            $adotado = AdoteMissionarioService::missionarioDaInstancia();
            $regiaoId = $request->filled('regiao_id') ? (int) $request->regiao_id : null;
            $agencia = in_array($request->agencia, ['jmn', 'apmt'], true) ? $request->agencia : null;

            return view('dashboard.adote-missionario.escolher', [
                'adotado' => $adotado,
                'missionarios' => $adotado
                    ? collect()
                    : AdoteMissionarioService::disponiveis($regiaoId, $agencia),
                'regioes' => Regiao::query()->orderBy('nome')->get(),
                'regiaoFiltro' => $regiaoId,
                'agenciaFiltro' => $agencia,
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function adotar(Missionario $missionario)
    {
        try {
            AdoteMissionarioService::adotar($missionario);

            return redirect()->route('dashboard.adote-missionario.escolher')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Missionário adotado com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.escolher')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function sincronizarJmn()
    {
        try {
            $resultado = AdoteMissionarioService::sincronizarJmn();

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => "Sincronização JMN concluída: {$resultado['criados']} criados, {$resultado['atualizados']} atualizados ({$resultado['total']} no total).",
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado na sincronização JMN!',
                ],
            ]);
        }
    }

    public function sincronizarApmt()
    {
        try {
            $resultado = AdoteMissionarioService::sincronizarApmt();

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => "Sincronização APMT concluída: {$resultado['criados']} criados, {$resultado['atualizados']} atualizados ({$resultado['total']} no total).",
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado na sincronização APMT!',
                ],
            ]);
        }
    }

    public function removerVinculos()
    {
        try {
            $afetados = AdoteMissionarioService::removerTodosVinculos();

            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => $afetados > 0
                        ? "{$afetados} vínculo(s) de adoção removido(s)."
                        : 'Nenhum vínculo de adoção para remover.',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.adote-missionario.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado ao remover os vínculos!',
                ],
            ]);
        }
    }
}
