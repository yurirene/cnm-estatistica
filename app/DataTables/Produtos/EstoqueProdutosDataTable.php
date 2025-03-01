<?php

namespace App\DataTables\Produtos;

use App\Helpers\BootstrapHelper;
use App\Models\Produtos\FluxoEstoqueProduto;
use App\Services\Produtos\EstoqueProdutoService;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EstoqueProdutosDataTable extends DataTable
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
                    'route' => 'dashboard.estoque-produtos',
                    'id' => $sql->id,
                    'confirmar' => !$sql->status
                ]);
            })
            ->editColumn('created_at', function ($sql) {
                return Carbon::parse($sql->created_at)->format('d/m/Y');
            })
            ->editColumn('tipo', function ($sql) {
                return BootstrapHelper::badge(
                    EstoqueProdutoService::LABELS_TIPOS[$sql->tipo],
                    EstoqueProdutoService::TIPOS[$sql->tipo]
                );
            })
            ->editColumn('quantidade', function ($sql) {
                return $sql->quantidade;
            })
            ->editColumn('produto_id', function ($sql) {
                return $sql->produto->nome;
            })
            ->rawColumns(['tipo']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Atividade $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FluxoEstoqueProduto $model)
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
                    ->setTableId('estoque-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax(route('dashboard.produtos.datatable.estoque'))
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create')
                            ->text('<i class="fas fa-plus"></i> Novo Registro')
                            ->action("window.location = '".route('dashboard.estoque-produtos.create')."';")
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
            Column::make('tipo')->title('Tipo'),
            Column::make('produto_id')->title('Produto'),
            Column::make('quantidade')->title('Quantidade'),
            Column::make('observacao')->title('Observação'),
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
