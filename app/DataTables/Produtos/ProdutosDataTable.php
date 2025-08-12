<?php

namespace App\DataTables\Produtos;

use App\Helpers\FormHelper;
use App\Models\Produtos\Produto;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProdutosDataTable extends DataTable
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
                return view('dashboard.produtos.actions', [
                    'route' => 'dashboard.produtos',
                    'id' => $sql->id,
                ]);
            })
            ->editColumn('valor', function ($sql) {
                return $sql->valor_formatado;
            })
            ->editColumn('exibir', function ($sql) {
                return FormHelper::statusFormatado($sql->exibir, 'Exibir', 'Não Exibir');
            })
            ->rawColumns(['exibir']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Produtos\Produto $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Produto $model)
    {
        return $model->newQuery()
            ->when(request()->filled('status'), function ($query) {
                return $query->where('exibir', request('status'));
            });
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('produtos-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('dashboard.produtos.datatable.produtos'))
            ->dom('Bfrtip')
            ->orderBy(2)
            ->buttons(
                Button::make('create')->text('<i class="fas fa-plus"></i> Novo Registro')
            )
            ->parameters([
                'stateSave'=>true,
                "language" => [
                    "url" => "/vendor/datatables/portugues.json"
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
            Column::make('valor')->title('Valor'),
            Column::make('estoque')->title('Estoque'),
            Column::make('exibir')->title('Exibir?'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Produtos_' . date('YmdHis');
    }
}
