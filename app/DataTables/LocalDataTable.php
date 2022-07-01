<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Local;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LocalDataTable extends DataTable
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
                    'route' => 'dashboard.locais',
                    'id' => $sql->id,
                    'show' => false
                ]);
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Ativo', 'Inativo');
            })
            ->editColumn('regiao_id', function($sql) {
                return $sql->regiao->nome;
            })
            ->editColumn('estado_id', function($sql) {
                return $sql->estado->nome;
            })
            ->editColumn('sinodal_id', function($sql) {
                return $sql->sinodal->sigla;
            })
            ->editColumn('federacao_id', function($sql) {
                return $sql->federacao->sigla;
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AcessoExterno $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Local $model)
    {
        return $model->newQuery()->minhaFederacao();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('ump-local-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova UMP')
                    )
                    ->parameters([
                        "language" => [
                            "url" => "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                        ]
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
            Column::make('nome')->title('Nome'),
            Column::make('federacao_id')->title('Federação'),
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('estado_id')->title('Estado'),
            Column::make('status')->title('Status'),
            Column::make('regiao_id')->title('Região'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'UMP_LOCAL_' . date('YmdHis');
    }
}
