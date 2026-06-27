<?php

namespace App\DataTables\Produtos;

use App\Helpers\FormHelper;
use App\Models\Produtos\ConsignacaoProduto;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ConsignacaoProdutosDataTable extends DataTable
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
                return view('dashboard.produtos.actions', [
                    'route' => 'dashboard.consignacao-produtos',
                    'id' => $sql->id,
                ]);
            })
            ->editColumn('created_at', function($sql) {
                return Carbon::parse($sql->created_at)->format('d/m/Y');
            })
            ->editColumn('produto_id', function($sql) {
                return $sql->produto->nome;
            })
            ->editColumn('user_id', function($sql) {
                return $sql->usuario->name;
            })
            ->editColumn('quantidade_consignada', function($sql) {
                return $sql->quantidade_consignada;
            })
            ->editColumn('quantidade_retornada', function($sql) {
                return $sql->quantidade_retornada;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Atividade $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ConsignacaoProduto $model)
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
                    ->setTableId('consignacao-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax(route('dashboard.produtos.datatable.consignacao'))
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->buttons(
                        Button::make('create')
                        ->text('<i class="fas fa-plus"></i> Novo Registro')
                        ->action("window.location = '".route('dashboard.consignacao-produtos.create')."';")
                    )
                    ->parameters([
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
            Column::make('created_at')->title('Criado em'),
            Column::make('produto_id')->title('Produto'),
            Column::make('user_id')->title('Usuário'),
            Column::make('quantidade_consignada')->title('Saída'),
            Column::make('quantidade_retornada')->title('Retorno'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'UMP_LOCAL_' . date('YmdHis');
    }
}
