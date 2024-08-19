<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BoostrapHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Digesto;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DigestoDataTable extends DataTable
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
                    'route' => 'dashboard.digestos',
                    'id' => $sql->id,
                    'delete' => true
                ]);
            })
            ->editColumn('arquivo', function($sql) {
                return BootstrapHelper::buttonLink('primary', 'Abrir', $sql->path, true);
            })
            ->editColumn('tipo_reuniao_id', function($sql) {
                return BootstrapHelper::badge('primary', $sql->tipo->nome);
            })
            ->rawColumns(['tipo_reuniao_id', 'arquivo']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Digesto $model)
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
                    ->setTableId('digesto-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Novo Digesto')
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
        $colunas = [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('titulo')->title('Título'),
            Column::make('ano')->title('Ano'),
            Column::make('tipo_reuniao_id')->title('Tipo')->searchable(false),
            Column::make('arquivo')->title('Arquivo')->searchable(false)->orderable(false),
        ];
        return $colunas;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'DIGESTOS_' . date('YmdHis');
    }
}
