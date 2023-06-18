<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\ComprovanteACI;
use App\Models\FormularioSinodal;
use App\Models\Parametro;
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
                    'status' => auth()->user()->roles->first()->name == 'tesouraria',
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
        return isset($formulario['aci']['valor_repassado']) ? 'R$' .$formulario['aci']['valor_repassado'] : 'Não Informado' ;
    }

    public function valorPrevisto($sql)
    {
        $formulario = FormularioSinodal::where('sinodal_id', $sql->sinodal_id)
            ->where('ano_referencia', $sql->ano)->first();
        if (is_null($formulario)) {
            return 'Formulário não respondido';
        }
        $total_de_socios = intval($formulario['perfil']['ativos']) + intval($formulario['perfil']['cooperadores']);
        $param_valor_aci = floatval(Parametro::where('nome', 'valor_aci')->first()->valor);
        return 'R$' . $total_de_socios * $param_valor_aci * 0.25;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ComprovanteACI $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComprovanteACI $model)
    {
        return $model->newQuery()->meusComprovantes();
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
            ->dom('Bfrtip')
            ->orderBy(2)
            ->parameters([
                "language" => [
                    "url" => "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
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
}
