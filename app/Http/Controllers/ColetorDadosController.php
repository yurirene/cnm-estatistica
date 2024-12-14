<?php

namespace App\Http\Controllers;

use App\DataTables\ColetorDadosDataTable;
use App\Exceptions\ColetaDadosException;
use App\Services\ColetorDadosService;
use Illuminate\Http\Request;

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
}
