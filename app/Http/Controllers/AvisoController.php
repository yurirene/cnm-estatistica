<?php

namespace App\Http\Controllers;

use App\DataTables\AvisosDataTable;
use App\Models\Aviso;
use App\Services\AvisoService;
use App\Services\DatatableAjaxService;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    public function index(AvisosDataTable $dataTable)
    {
        try {
            return $dataTable->render('dashboard.avisos.index',[
                'tipos' => Aviso::TIPOS
            ]);
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
            AvisoService::store($request->all());
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Aviso criado com sucesso!'
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

    public function delete($id)
    {
        try {
            AvisoService::delete($id);
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Aviso removido com sucesso!'
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

    public function getUsuarios()
    {
        return response()->json(['results' => AvisoService::getUsuarios()], 200);
    }

    public function visualizado($id)
    {
        return response()->json(AvisoService::visualizado($id), 200);
    }

    public function listarVisualizados($id)
    {
        return DatatableAjaxService::listarVisualizados($id);
    }
}
