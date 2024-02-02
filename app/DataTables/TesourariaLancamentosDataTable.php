<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\User;
use App\Services\UserService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TesourariaLancamentosDataTable extends DataTable
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
            // ->addColumn('action', function($sql) {
            //     return view('includes.actions', [
            //         'route' => 'dashboard.usuarios',
            //         'id' => $sql->id,
            //         'delete' => false
            //     ]);
            // })
            ->editColumn('tipo', function($sql) {
                return FormHelper::statusFormatado($sql->tipo, 'Entrada', 'Saída');
            })
            ->editColumn('categoria_id', function ($sql) {
                if (!is_null($sql->categoria_id)) {
                    return BootstrapHelper::badge('primary', $sql->categoria->nome, true);
                }
                return '';
            })
            ->rawColumns(['status', 'perfil', 'administrando']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Tesouraria\Lancamento $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lancamento $model)
    {
        return $model->newQuery()->daMinhaInstancia();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('lancamento-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->buttons(
                        Button::make('create')
                            ->text('<i class="fas fa-plus"></i> Novo Lançamento')
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
            Column::make('data_lancamento')->title('Data Lançamento'),
            Column::make('tipo')->title('Tipo'),
            Column::make('descricao')->title('Descrição'),
            Column::make('valor')->title('Valor'),
            Column::make('categoria_id')->title('Categoria'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'TESOURARIA_' . date('YmdHis');
    }
}
