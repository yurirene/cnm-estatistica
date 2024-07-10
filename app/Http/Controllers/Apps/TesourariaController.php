<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\TesourariaLancamentosDataTable;
use App\Exports\TesourariaExport;
use App\Http\Controllers\Controller;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Services\Apps\TesourariaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpFoundationRedirectResponse;
use Throwable;

class TesourariaController extends Controller
{
    /**
     * /dashboard/apps/tesouraria/index
     *
     * @param TesourariaLancamentosDataTable $dataTable
     *
     * @return mixed
     */
    public function index(TesourariaLancamentosDataTable $dataTable)
    {
        return $dataTable->render('dashboard.apps.tesouraria.index', [
            'categorias' => TesourariaService::categoriaToSelect(),
            'totalizadores' => TesourariaService::totalizadores(),
            'mesPassado' => Carbon::now()->subMonth()->format('m/Y'),
            'tipos' => TesourariaService::getTipos()
        ]);
    }

    /**
     * /dashboard/apps/tesouraria/create
     *
     * @return View
     */
    public function create(): View
    {
        return view('dashboard.apps.tesouraria.form', [
            'categorias' => TesourariaService::categoriaToSelect(),
            'tipos' => TesourariaService::getTipos()
        ]);
    }

    /**
     * dashboard/apps/tesouraria/store
     *
     * @param Request $request
     *
     * @return HttpFoundationRedirectResponse
     */
    public function store(Request $request): HttpFoundationRedirectResponse
    {
        $request->validate(
            [
                'comprovante' => 'mimes:pdf|max:300', // 300 Kb
            ],
            [
                '*.mimes' => 'O comprovante precisa ser um PDF',
                '*.max' => 'O comprovante precisa ter no máximo 300 Kb'
            ]
        );

        try {
            TesourariaService::store($request->all());
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
     * /dashboard/apps/tesouraria/edit
     *
     * @param Lancamento $tesourarium
     *
     * @return View
     */
    public function edit(Lancamento $tesourarium): View
    {
        return view('dashboard.apps.tesouraria.form', [
            'categorias' => TesourariaService::categoriaToSelect(),
            'lancamento' => $tesourarium,
            'tipos' => TesourariaService::getTipos()
        ]);
    }

    /**
     * /dashboard/apps/tesouraria/update
     *
     * @param Lancamento $tesourarium
     * @param Request $request
     *
     * @return HttpFoundationRedirectResponse
     */
    public function update(Lancamento $tesourarium, Request $request): HttpFoundationRedirectResponse
    {
        try {
            TesourariaService::update($request->all(), $tesourarium);
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
     * /dashboard/apps/tesouraria/delete
     *
     * @param Lancamento $tesourarium
     *
     * @return HttpFoundationRedirectResponse
     */
    public function delete(Lancamento $tesourarium): HttpFoundationRedirectResponse
    {
        try {
            TesourariaService::delete($tesourarium);
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
     * /dashboard/apps/tesouraria/gerar-relatorio
     *
     * @return void
     */
    public function gerarRelatorio(Request $request)
    {
        return Excel::download(new TesourariaExport($request->all()), 'relatorio.csv', ExcelExcel::CSV);
    }
}
