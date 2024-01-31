<?php

namespace App\Http\Controllers;

use App\Models\Diretoria;
use App\Models\DiretoriaInformacao;
use App\Services\DiretoriaService;
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
        $diretoria = DiretoriaService::getDiretoriaVigente();
        return view('dashboard.diretoria.index', [
            'cargos' => Diretoria::LABELS,
            'diretoria' => $diretoria
        ]);
    }

    public function salvar()
    {
        DB::beginTransaction();
        try {
            $di = Diretoria::create([
                'presidente' => 'teste_presidente',
                'vice_presidente' => 'teste_vice_presidente',
                'primeiro_secretario' => 'teste_primeiro_secretario',
                'segundo_secretario' => 'teste_segundo_secretario',
                'secretario_executivo' => 'teste_secretario_executivo',
                'tesoureiro' => 'teste_tesoureiro',
                'secretario_causas' => 'teste_secretario_causas',
                'sinodal_id' => 'b3201496-329d-43b4-82ba-8ead42f25b1f',
                'ano' => 2023
            ]);
            $i= 1;
            $retorno = [];
            foreach (array_keys(Diretoria::LABELS) as $chave) {
                $retorno["contato_$chave"] = '(92)99990999' . $i;
                $retorno["path_$chave"] = "https://picsum.photos/300/300?image=" . $i;
                $i++;
            }
            $retorno['diretoria_id'] = $di->id;
            DiretoriaInformacao::create($retorno);
            DB::commit();
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Operação realizada com Sucesso!'
                ]
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!'
                ]
            ])
            ->withInput();
        }
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
            dd($th->getMessage(), $th->getLine(), $th->getFile());
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
