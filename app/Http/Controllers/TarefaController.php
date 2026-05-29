<?php

namespace App\Http\Controllers;

use App\DataTables\TarefasDataTable;
use App\Http\Requests\Tarefas\StoreTarefaRequest;
use App\Http\Requests\Tarefas\UpdateTarefaRequest;
use App\Models\Tarefa;
use App\Services\LogErroService;
use App\Services\TarefaService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class TarefaController extends Controller
{
    public function index(TarefasDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.tarefas.index', [
                'estatisticas' => TarefaService::estatisticas(),
                'periodos' => TarefaService::opcoesPeriodo(),
                'status' => TarefaService::opcoesStatus(),
                'usuario' => auth()->user(),
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu errado!',
                ],
            ]);
        }
    }

    public function store(StoreTarefaRequest $request): RedirectResponse
    {
        try {
            TarefaService::criar($request->validated(), $request->user());

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Tarefa criada com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível criar a tarefa.',
                ],
            ])->withInput();
        }
    }

    public function update(UpdateTarefaRequest $request, Tarefa $tarefa): RedirectResponse
    {
        try {
            TarefaService::atualizar($tarefa, $request->validated());

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Tarefa atualizada com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível atualizar a tarefa.',
                ],
            ])->withInput();
        }
    }

    public function destroy(Tarefa $tarefa): RedirectResponse
    {
        if (!TarefaService::pertenceAoUsuario($tarefa)) {
            abort(403);
        }

        try {
            TarefaService::excluir($tarefa);

            return redirect()->route('dashboard.tarefas.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Tarefa removida com sucesso.',
                ],
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível remover a tarefa.',
                ],
            ]);
        }
    }

    public function encerrar(Tarefa $tarefa): RedirectResponse
    {
        if (!TarefaService::pertenceAoUsuario($tarefa)) {
            abort(403);
        }

        TarefaService::encerrar($tarefa);

        return redirect()->back()->with([
            'mensagem' => [
                'status' => true,
                'texto' => 'Tarefa concluída.',
            ],
        ]);
    }
}
