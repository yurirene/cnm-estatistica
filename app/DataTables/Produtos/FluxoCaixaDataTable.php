<?php

namespace App\DataTables\Produtos;

use App\Helpers\BootstrapHelper;
use App\Models\Produtos\FluxoCaixa;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FluxoCaixaDataTable extends DataTable
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
            ->addColumn('action', function ($sql) {
                return view('dashboard.produtos.fluxo-caixa.actions', [
                    'route' => 'dashboard.produtos.fluxo-caixa',
                    'id' => $sql->id,
                    'delete' => true,
                    'edit' => true
                ]);
            })
            ->editColumn('tipo', function ($sql) {
                return BootstrapHelper::badge(
                    FluxoCaixa::LABELS_TIPOS[$sql->tipo],
                    FluxoCaixa::TIPOS[$sql->tipo]
                );
            })
            ->editColumn('valor', function ($sql) {
                return 'R$' . $sql->valor;
            })

            ->editColumn('created_at', function ($sql) {
                return $sql->created_at->format('d/m/Y');
            })
            ->rawColumns(['tipo']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\FluxoCaixa $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FluxoCaixa $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('fluxo-caixa-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax(route('dashboard.produtos.datatable.fluxo-caixa'))
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->parameters([
                        "buttons" => [
                            [
                                'text' => '<i class="fas fa-plus"></i> Novo Registro',
                                'action' => "function() { window.location.href = '"
                                    . route('dashboard.produtos.fluxo-caixa.create')
                                    . "'}"
                            ]
                        ],
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
            Column::make('created_at')->title('Data'),
            Column::make('tipo')->title('Tipo'),
            Column::make('descricao')->title('Descrição'),
            Column::make('valor')->title('Valor'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'FluxoCaixa_' . date('YmdHis');
    }
}
