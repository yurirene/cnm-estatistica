<?php

namespace App\Http\Controllers;

use App\Services\SecretarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class SecretarioController extends Controller
{

    /**
     * Endpoint responsável por adicionar ou atualizar um secretário
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeUpdate(Request $request): RedirectResponse
    {
        try {
            if (!$request->filled('secretario_id')) {
                SecretarioService::store($request->toArray());
            } else {
                SecretarioService::update($request->toArray());
            }
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => self::MSG_SUCESSO
                ],
                'aba' => 'secretarios'
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => self::MSG_ERRO
                ]
            ]);
        }

    }


    /**
     * Endpoint responsável por remover o secretario
     *
     * @param string $secretario
     * @return RedirectResponse
     */
    public function delete(string $secretario): RedirectResponse
    {
        try {
            SecretarioService::delete($secretario);
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => self::MSG_SUCESSO
                ],
                'aba' => 'secretarios'
            ]);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.diretoria.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => self::MSG_ERRO
                ]
            ]);
        }
    }
}
