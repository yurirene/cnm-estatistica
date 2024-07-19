<?php

namespace App\Http\Controllers;

use App\Models\Diretoria;
use App\Models\DiretoriaInformacao;
use App\Services\DiretoriaService;
use App\Services\SecretarioService;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DiretoriaController extends Controller
{
    public function index(): View
    {
        $diretoria = DiretoriaService::getDadosDiretoria();
        $secretarios = SecretarioService::getSecretariosDaDiretoria($diretoria['id']);
        return view('dashboard.diretoria.index', [
            'diretoria' => $diretoria,
            'secretarios' => $secretarios
        ]);
    }

    /**
     * Responsável por criar uma nova diretoria para gestao e salvar os dados no histórico
     *
     * @param Request $request dados da requisção
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DiretoriaService::salvarNoHistorico();
            DiretoriaService::novaDiretoria($request->ano);
            return response()->json([
                'data' => [],
                'msg' => 'Diretoria Cadastrada com Sucesso'
            ], 200);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'data' => [],
                'msg' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * Responsável por atualizar o cargo da diretoria
     *
     * @param Request $request dados da requisção
     * @return
     */
    public function update(Request $request)
    {
        try {
            DiretoriaService::update($request->toArray());
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ],
                'aba' => 'diretoria'
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ]);
        }
    }

    /**
     * Responsável por validar se já existe diretoria cadastrada no ano informado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validarAnoDaNovaDiretoria(Request $request): JsonResponse
    {
        try {
            $data = [
                'data' => DiretoriaService::validarAnoDaDiretoria(intval($request->ano))
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
