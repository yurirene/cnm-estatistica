<?php

namespace App\Http\Controllers;

use App\DataTables\SugestoesDataTable;
use App\Services\HelpdeskService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Throwable;

class HelpdeskController extends Controller
{
    public function index(SugestoesDataTable $dataTable)
    {
        return $dataTable->render('dashboard.helpdesk.index');
    }

    /**
     * Salvar a sugestão
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            HelpdeskService::storeSugestao($request->all());
            HelpdeskService::enviarNotificacao($request->all());
            return redirect()->route('dashboard.helpdesk.index')->with([
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
