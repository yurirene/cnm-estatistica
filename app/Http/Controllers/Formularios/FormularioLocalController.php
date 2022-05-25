<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormularioLocalRequest;
use App\Services\Formularios\FormularioLocalService;
use Illuminate\Http\Request;
use Throwable;

class FormularioLocalController extends Controller
{
    public function index()
    {
        return view('dashboard.formularios.local');
    }

    public function store(StoreFormularioLocalRequest $request)
    {
        try {
            FormularioLocalService::store($request);
            return redirect()->route('dashboard.locais.index')->with([
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
