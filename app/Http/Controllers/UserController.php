<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Models\Perfil;
use App\Models\Regiao;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('dashboard.usuarios.index');
    }

    public function create()
    {
        return view('dashboard.usuarios.form',[
            'regioes' => UserService::getRegioes()->pluck('nome', 'id'),
            'perfis' => UserService::getPerfis()->pluck('descricao', 'id')
        ]);
    }

    public function store(Request $request)
    {
        try {
            UserService::store($request);
            return redirect()->route('dashboard.usuarios.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }


    public function edit(User $usuario)
    {
        return view('dashboard.usuarios.form',[
            'usuario' => $usuario,
            'regioes' => UserService::getRegioes()->pluck('nome', 'id'),
            'perfis' => UserService::getPerfis()->pluck('descricao', 'id')
        ]);
    }

    public function update(User $usuario, Request $request)
    {
        try {
            UserService::update($usuario, $request);
            return redirect()->route('dashboard.usuarios.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
    }
}
