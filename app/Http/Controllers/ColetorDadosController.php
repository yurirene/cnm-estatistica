<?php

namespace App\Http\Controllers;

use App\DataTables\ColetorDadosDataTable;
use App\Exceptions\ColetaDadosException;
use App\Models\Local;
use App\Models\User;
use App\Services\ColetorDadosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ColetorDadosController extends Controller
{
    public function index(ColetorDadosDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.coletor-dados.index');
        } catch (\Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            ColetorDadosService::store($request->all());
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Formulários foram criados com sucesso!'
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

    public function responder(string $id, Request $request)
    {
        $mensagem = '';
        
        try {
            ColetorDadosService::responder($id, $request->except(['_token']));

            return redirect()->route('coletor-dados.login')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Formulário Respondido com Sucesso!'
                ]
            ]);
        } catch (ColetaDadosException $e) {
            $mensagem = $e->getMessage();
        } catch (\Throwable $th) {
            $mensagem = 'Algo deu Errado!';
        }

        return redirect()->route('coletor-dados.externo', ['codigo' => $id])
            ->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $mensagem
                ]
            ]);
    }

    public function delete(string $id)
    {
        try {
            ColetorDadosService::delete($id);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Formulário removido com sucesso!'
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

    public function login()
    {
        return view('coletor-dados.login');
    }


    public function externo(Request $request)
    {
        try {
            $request->validate([
                'codigo' => 'required'
            ]);

            $dados = ColetorDadosService::carregar($request->codigo);

            return view('coletor-dados.formulario', $dados);
        } catch (\Throwable $th) {
            return redirect()->route('coletor-dados.login')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    public function restaurar(string $local)
    {
        try {
            $localModel = Local::query()->findOrFail($local);
            $this->authorizeColetorRestaurar($localModel);

            ColetorDadosService::restaurar($localModel->id);

            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Todos os formulários foram removidos com sucesso!'
                ]
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    private function authorizeColetorRestaurar(Local $local): void
    {
        $user = auth()->user();

        if ($user === null) {
            abort(403);
        }

        if (Gate::allows('isAdmin') || $user->role->name === User::ROLE_DIRETORIA) {
            return;
        }

        if ($user->local_id && $user->local_id === $local->id) {
            return;
        }

        if ($user->federacao_id && $user->federacao_id === $local->federacao_id) {
            return;
        }

        if ($user->sinodal_id && $user->sinodal_id === $local->sinodal_id) {
            return;
        }

        abort(403, 'Você não tem permissão para restaurar os dados deste local.');
    }
}
