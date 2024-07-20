<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Tesouraria\Categoria;
use App\Services\Apps\TesourariaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CategoriaController extends Controller
{

    /**
     * dashboard/apps/tesouraria/categoria/store
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            TesourariaService::salvarCategoria($request->all());
            return redirect()->route('dashboard.apps.tesouraria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => self::MSG_SUCESSO
                ]
                ]);
        } catch (Throwable $th) {
            dd($th->getMessage());
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => self::MSG_ERRO
                ]
            ])
            ->withInput();
        }
    }

    /**
     * /dashboard/apps/tesouraria/categoria/edit
     *
     * @param Categoria $categorium
     *
     * @return View
     */
    public function edit(Categoria $categorium): View
    {
        return view('dashboard.apps.tesouraria.categoria-form', [
            'categoria' => $categorium
        ]);
    }

    /**
     * /dashboard/apps/tesouraria/categoria/update
     *
     * @param Categoria $categorium
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function update(Categoria $categorium, Request $request): RedirectResponse
    {
        try {
            TesourariaService::atualizarCategoria($request->all(), $categorium);
            return redirect()->route('dashboard.apps.tesouraria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => self::MSG_SUCESSO
                ]
                ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => self::MSG_ERRO
                ]
            ])
            ->withInput();
        }
    }

    /**
     * /dashboard/apps/tesouraria/categoria/delete
     *
     * @param Categoria $categorium
     *
     * @return RedirectResponse
     */
    public function delete(Categoria $categoria): RedirectResponse
    {
        try {
            TesourariaService::removerCategoria($categoria);
            return redirect()->route('dashboard.apps.tesouraria.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => self::MSG_SUCESSO
                ]
                ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => self::MSG_ERRO
                ]
            ])
            ->withInput();
        }
    }
}
