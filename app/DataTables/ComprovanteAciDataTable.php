<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\ComprovanteACI;
use App\Models\FormularioSinodal;
use App\Models\Parametro;
use App\Models\User;
use App\Services\ComprovanteAciService;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ComprovanteAciDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function($sql) {
                return view('includes.actions', [
                    'route' => 'dashboard.comprovante-aci',
                    'id' => $sql->id,
                    'status' => auth()->user()->role->name == 'tesouraria',
                    'edit' => false,
                    'delete' => false,
                    'abrir' => $sql->path
                ]);
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Confirmado', 'Pendente');
            })
            ->editColumn('sinodal_id', function($sql) {
                return $sql->sinodal->sigla;
            })
            ->addColumn('valor_informado', function($sql) {
                return $this->valorInformado($sql);
            })
            ->addColumn('created_at', function($sql) {
                return $sql->created_at->format('d/m/Y H:i:s');
            })
            ->addColumn('valor_previsto', function($sql) {
                return $this->valorPrevisto($sql);
            })
            ->rawColumns(['status']);
    }

    public function valorInformado($sql)
    {
        $formulario = FormularioSinodal::where('sinodal_id', $sql->sinodal_id)
            ->where('ano_referencia', $sql->ano)->first();
        if (is_null($formulario)) {
            return 'Formulário não respondido';
        }
        return isset($formulario['aci']['valor_repassado'])
            ? 'R$' . $formulario['aci']['valor_repassado']
            : 'Não Informado';
    }

    public function valorPrevisto($sql)
    {
        $formulario = FormularioSinodal::where('sinodal_id', $sql->sinodal_id)
            ->where('ano_referencia', $sql->ano)->first();
        if (is_null($formulario)) {
            return 'Formulário não respondido';
        }
        $totalSocios = intval($formulario['perfil']['ativos']) + intval($formulario['perfil']['cooperadores']);
        $paramValorAci = floatval(Parametro::where('nome', 'valor_aci')->first()->valor);
        $valorPrevisto = $totalSocios * $paramValorAci * 0.25;
        return 'R$' . number_format($valorPrevisto, 2, ',', '.');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ComprovanteACI $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComprovanteACI $model)
    {
        $filtro = json_decode(request()->get('filtro'), true);
        return $model->newQuery()->meusComprovantes()
            ->when(!empty($filtro['ano_referencia']), function($sql) use ($filtro) {
                $sql->where('ano', $filtro['ano_referencia']);
            })
            ->when(!empty($filtro['data_criacao']), function($sql) use ($filtro) {
                $datas = explode(' - ', $filtro['data_criacao']);
                $periodo[0] = Carbon::createFromFormat('d/m/Y', $datas[0]);
                $periodo[1] = Carbon::createFromFormat('d/m/Y', $datas[1]);
                return $sql->whereBetween('created_at', $periodo);
            })
            ->when(!empty($filtro['status']) && $filtro['status'] != 'T', function($sql) use ($filtro) {
                return $sql->where('status', $filtro['status'] == 'C');
            })
            ->orderBy('ano', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('comprovantes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtipl')
            ->orderBy(2)
            ->parameters([
                "language" => [
                    "url" => "/vendor/datatables/portugues.json"
                ],
                'buttons' => [],
                'responsive' => true

            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('ano')->title('Ano Referência'),
            Column::make('valor_informado')->title('Valor Informado'),
            Column::make('valor_previsto')->title('Valor Previsto'),
            Column::make('status')->title('Status'),
            Column::make('created_at')->title('Cadastrado em'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Comprovante_ACI_' . date('YmdHis');
    }


    /**
     * Retorna os dados dos filtros da tabela
     *
     * @return array
     */
    public function filtros(): array
    {
        if (!$this->verificaSeUsarioTesouraria()) {
            return [];
        }
        $status = [
            'T' => 'Todos',
            'P' => 'Pendentes',
            'C' => 'Confirmados'
        ];
        $anosCadastrados = ComprovanteAciService::getAnosCadastrados();
        return [
            'data_criacao' => true,
            'ano_referencia' => ["" => "Todos"] + $anosCadastrados,
            'status' => $status
        ];
    }

    /**
     * Verifica se o usário é do perfil tesouraria
     *
     * @return boolean
     */
    public function verificaSeUsarioTesouraria(): bool
    {
        return auth()->user()->role->name == User::ROLE_TESOURARIA;
    }
}
