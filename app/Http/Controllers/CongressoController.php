<?php

namespace App\Http\Controllers;

use App\DataTables\Congresso\DelegadosCongressoDataTable;
use App\DataTables\Congresso\ReunioesCongressoDataTable;
use App\DataTables\Congresso\ReunioesCongressoNacionalDataTable;
use App\Models\Congresso\DelegadoCongresso;
use App\Models\Congresso\ReuniaoCongresso;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CongressoController extends Controller
{
    public function index(Request $request)
    {
        $dataTable = new ReunioesCongressoDataTable();
        $isSinodal = auth()->user()->role->name === 'sinodal';

        return view('dashboard.congresso.index', compact('dataTable', 'isSinodal'));
    }

    public function indexNacional(Request $request)
    {
        $dataTable = new ReunioesCongressoNacionalDataTable();

        return view('dashboard.congresso.nacional', compact('dataTable'));
    }

    public function create()
    {
        $sinodais = Sinodal::orderBy('nome')->get();
        $federacoes = Federacao::orderBy('nome')->get();
        $locais = Local::orderBy('nome')->get();

        return view('dashboard.congresso.form', compact('sinodais', 'federacoes', 'locais'));
    }

    public function createNacional()
    {
        return view('dashboard.congresso.form-nacional');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ano' => 'required|string|max:4',
            'local' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'diretoria' => 'boolean',
            'relatorio_estatistico' => 'boolean',
            'tipo_congresso' => 'required|in:nacional,sinodal,federacao,local',
            'sinodal_id' => 'required_if:tipo_congresso,sinodal|nullable|exists:sinodais,id',
            'federacao_id' => 'required_if:tipo_congresso,federacao|nullable|exists:federacoes,id',
            'local_id' => 'required_if:tipo_congresso,local|nullable|exists:locais,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'ano', 'local', 'descricao', 'data_inicio', 'data_fim',
                'diretoria', 'relatorio_estatistico'
            ]);

            $data['id'] = Str::uuid();
            $data['aberto'] = true;
            $data['status'] = 1;

            // Definir campos de instância baseado no tipo
            if ($request->tipo_congresso === 'sinodal') {
                $data['sinodal_id'] = $request->sinodal_id;
            } elseif ($request->tipo_congresso === 'federacao') {
                $data['federacao_id'] = $request->federacao_id;
            } elseif ($request->tipo_congresso === 'local') {
                $data['local_id'] = $request->local_id;
            }

            ReuniaoCongresso::create($data);

            DB::commit();

            return redirect()->route('dashboard.congresso.index')
                ->with('success', 'Congresso criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar congresso: ' . $e->getMessage());
        }
    }

    public function storeNacional(Request $request)
    {
        $request->validate([
            'ano' => 'required|string|max:4',
            'local' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'diretoria' => 'boolean',
            'relatorio_estatistico' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'ano', 'local', 'descricao', 'data_inicio', 'data_fim',
                'diretoria', 'relatorio_estatistico'
            ]);

            $data['id'] = Str::uuid();
            $data['aberto'] = true;
            $data['status'] = 1;
            // Para congresso nacional, todos os campos de instância ficam nulos

            ReuniaoCongresso::create($data);

            DB::commit();

            return redirect()->route('dashboard.congresso-nacional.index')
                ->with('success', 'Congresso Nacional criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar congresso nacional: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $reuniao = ReuniaoCongresso::with(['sinodal', 'federacao', 'local'])->findOrFail($id);
        $dataTable = new DelegadosCongressoDataTable($id);

        return view('dashboard.congresso.show', compact('reuniao', 'dataTable'));
    }

    public function edit($id)
    {
        $reuniao = ReuniaoCongresso::findOrFail($id);
        $sinodais = Sinodal::orderBy('nome')->get();
        $federacoes = Federacao::orderBy('nome')->get();
        $locais = Local::orderBy('nome')->get();

        return view('dashboard.congresso.form', compact('reuniao', 'sinodais', 'federacoes', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $reuniao = ReuniaoCongresso::findOrFail($id);

        $request->validate([
            'ano' => 'required|string|max:4',
            'local' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'diretoria' => 'boolean',
            'relatorio_estatistico' => 'boolean',
            'aberto' => 'boolean',
            'status' => 'required|integer|in:0,1,2',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'ano', 'local', 'descricao', 'data_inicio', 'data_fim',
                'diretoria', 'relatorio_estatistico', 'aberto', 'status'
            ]);

            $reuniao->update($data);

            DB::commit();

            return redirect()->route('dashboard.congresso.index')
                ->with('success', 'Congresso atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar congresso: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $reuniao = ReuniaoCongresso::findOrFail($id);

            if (!$reuniao->documentos->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Não é possível excluir um congresso que possui documentos vinculados.');
            }

            $reuniao->delete();

            return redirect()->route('dashboard.congresso.index')
                ->with('success', 'Congresso excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir congresso: ' . $e->getMessage());
        }
    }

    public function delegadosDatatable($reuniao)
    {
        $dataTable = new DelegadosCongressoDataTable($reuniao);
        return $dataTable->ajax();
    }

    public function delegadoCreate($reuniao)
    {
        $reuniaoModel = ReuniaoCongresso::findOrFail($reuniao);
        $sinodais = Sinodal::orderBy('nome')->get();
        $federacoes = Federacao::orderBy('nome')->get();
        $locais = Local::orderBy('nome')->get();

        return view('dashboard.congresso.delegado-form', compact('reuniaoModel', 'sinodais', 'federacoes', 'locais'));
    }

    public function delegadoStore(Request $request, $reuniao)
    {
        $reuniaoModel = ReuniaoCongresso::findOrFail($reuniao);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'telefone' => 'nullable|string|max:20',
            'suplente' => 'boolean',
            'tipo_instancia' => 'required|in:sinodal,federacao,local',
            'sinodal_id' => 'required_if:tipo_instancia,sinodal|nullable|exists:sinodais,id',
            'federacao_id' => 'required_if:tipo_instancia,federacao|nullable|exists:federacoes,id',
            'local_id' => 'required_if:tipo_instancia,local|nullable|exists:locais,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['nome', 'cpf', 'telefone', 'suplente']);
            $data['id'] = Str::uuid();
            $data['reuniao_id'] = $reuniao;
            $data['status'] = 1;

            // Definir campos de instância baseado no tipo
            if ($request->tipo_instancia === 'sinodal') {
                $data['sinodal_id'] = $request->sinodal_id;
            } elseif ($request->tipo_instancia === 'federacao') {
                $data['federacao_id'] = $request->federacao_id;
            } elseif ($request->tipo_instancia === 'local') {
                $data['local_id'] = $request->local_id;
            }

            DelegadoCongresso::create($data);

            DB::commit();

            return redirect()->route('dashboard.congresso.show', $reuniao)
                ->with('success', 'Delegado adicionado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao adicionar delegado: ' . $e->getMessage());
        }
    }

    public function delegadoDestroy($reuniao, $delegado)
    {
        try {
            $delegadoModel = DelegadoCongresso::where('reuniao_id', $reuniao)
                ->where('id', $delegado)
                ->firstOrFail();

            $delegadoModel->delete();

            return redirect()->route('dashboard.congresso.show', $reuniao)
                ->with('success', 'Delegado removido com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao remover delegado: ' . $e->getMessage());
        }
    }
}
