<?php

namespace App\Http\Controllers;

use App\DataTables\AuxilioPmf\PropostasDataTable;
use App\Http\Requests\AuxilioPmf\StoreEditalRequest;
use App\Http\Requests\AuxilioPmf\StorePropostaRequest;
use App\Http\Requests\AuxilioPmf\StoreRelatorioRequest;
use App\Http\Requests\AuxilioPmf\UpdateEditalRequest;
use App\Models\AuxilioPmf\AuxilioPmfEdital;
use App\Models\AuxilioPmf\AuxilioPmfProposta;
use App\Services\AuxilioPmfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class AuxilioPmfController extends Controller
{
    public function index(PropostasDataTable $dataTable)
    {
        try {
            $edital = AuxilioPmfService::getEditalVigente();
            $isAdmin = AuxilioPmfService::isAdmin();
            $isSinodal = AuxilioPmfService::isSinodal();
            $proposta = ($edital && $isSinodal)
                ? AuxilioPmfService::propostaDaSinodal($edital)
                : null;

            $viewData = [
                'edital' => $edital,
                'isAdmin' => $isAdmin,
                'isSinodal' => $isSinodal,
                'proposta' => $proposta,
                'podeEnviarProposta' => $edital && $isSinodal
                    ? AuxilioPmfService::podeEnviarProposta($edital)
                    : false,
                'podeSubstituirProposta' => $proposta
                    ? AuxilioPmfService::podeSubstituirProposta($proposta)
                    : false,
                'temRejeicaoNoAno' => $edital && $isSinodal
                    ? AuxilioPmfService::temRejeicaoNoAno((int) $edital->ano)
                    : false,
                'editais' => $isAdmin
                    ? AuxilioPmfEdital::query()->orderByDesc('ano')->orderByDesc('created_at')->get()
                    : collect(),
            ];

            if ($isAdmin) {
                return $dataTable->render('dashboard.auxilio-pmf.index', $viewData);
            }

            return view('dashboard.auxilio-pmf.index', $viewData);
        } catch (Throwable $th) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function create(): View|RedirectResponse
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        return view('dashboard.auxilio-pmf.form');
    }

    public function store(StoreEditalRequest $request): RedirectResponse
    {
        try {
            AuxilioPmfService::storeEdital($request);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Edital cadastrado com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ])->withInput();
        }
    }

    public function edit(AuxilioPmfEdital $edital): View|RedirectResponse
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        return view('dashboard.auxilio-pmf.form', [
            'edital' => $edital,
        ]);
    }

    public function update(UpdateEditalRequest $request, AuxilioPmfEdital $edital): RedirectResponse
    {
        try {
            AuxilioPmfService::updateEdital($edital, $request);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Edital atualizado com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ])->withInput();
        }
    }

    public function encerrar(AuxilioPmfEdital $edital): RedirectResponse
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        try {
            AuxilioPmfService::encerrar($edital);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Recebimento de propostas encerrado!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function storeProposta(StorePropostaRequest $request, AuxilioPmfEdital $edital): RedirectResponse
    {
        try {
            AuxilioPmfService::storeProposta($request, $edital);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Proposta enviada com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function destroyProposta(AuxilioPmfProposta $proposta): RedirectResponse
    {
        if (!AuxilioPmfService::isSinodal() || $proposta->sinodal_id !== auth()->user()->sinodal_id) {
            abort(403);
        }

        try {
            AuxilioPmfService::excluirArquivoProposta($proposta);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Arquivo da proposta excluído!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function storeRelatorio(StoreRelatorioRequest $request, AuxilioPmfProposta $proposta): RedirectResponse
    {
        if ($proposta->sinodal_id !== auth()->user()->sinodal_id) {
            abort(403);
        }

        try {
            AuxilioPmfService::storeRelatorio($request, $proposta);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Relatório enviado com sucesso!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function destroyRelatorio(AuxilioPmfProposta $proposta): RedirectResponse
    {
        if (!AuxilioPmfService::isSinodal() || $proposta->sinodal_id !== auth()->user()->sinodal_id) {
            abort(403);
        }

        try {
            AuxilioPmfService::excluirArquivoRelatorio($proposta);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Arquivo do relatório excluído!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function aprovar(AuxilioPmfProposta $proposta): RedirectResponse
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        try {
            AuxilioPmfService::aprovar($proposta);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Proposta aprovada!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function rejeitar(AuxilioPmfProposta $proposta): RedirectResponse
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        try {
            AuxilioPmfService::rejeitar($proposta);

            return redirect()->route('dashboard.auxilio-pmf.index')->with([
                'mensagem' => [
                    'status' => true,
                    'texto' => 'Proposta rejeitada!',
                ],
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage() ?: 'Algo deu Errado!',
                ],
            ]);
        }
    }

    public function propostasDatatable(PropostasDataTable $dataTable)
    {
        if (!AuxilioPmfService::isAdmin()) {
            abort(403);
        }

        return $dataTable->ajax();
    }
}
