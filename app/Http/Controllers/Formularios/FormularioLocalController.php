<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormularioLocalRequest;
use App\Models\Parametro;
use App\Services\Formularios\FormularioLocalService;
use Illuminate\Http\Request;
use Throwable;

class FormularioLocalController extends Controller
{
    public function index()
    {
        $formulario_respondido_ano = FormularioLocalService::getAnosFormulariosRespondidos();
        return view('dashboard.formularios.local', [
            'coleta' => FormularioLocalService::verificarColeta(),
            'anos' => $formulario_respondido_ano
        ]);
    }

    public function store(StoreFormularioLocalRequest $request)
    {
        try {
            FormularioLocalService::store($request);
            return redirect()->route('dashboard.formularios-locais.index')->with([
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

    public function view(Request $request)
    {
        try {
            $response = FormularioLocalService::showFormulario($request->id);
            return response()->json(['data' => $response]);
        } catch (\Throwable $th) {
            return response()->json(['erro' => $th->getMessage()]);
        }
    }
}
