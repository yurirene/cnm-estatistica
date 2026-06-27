<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Aviso;
use App\Models\Estatistica\Ranking;
use App\Models\Sinodal;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AvisosDataTable extends DataTable
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
                return view('dashboard.avisos.actions', [
                    'route' => 'dashboard.avisos',
                    'id' => $sql->id
                ]);
            })
            ->editColumn('titulo', function ($sql) {
                return $sql->titulo;
            })
            ->editColumn('texto', function ($sql) {
                return $sql->texto;
            })
            ->editColumn('tipo', function ($sql) {
                return  $sql->tipo_formatado;
            })
            ->rawColumns(['texto']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AcessoExterno $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Aviso $model)
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
                    ->setTableId('aviso-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->pageLength(20)
                    ->orderBy(1, 'asc')
                    ->parameters([
                        "buttons" => [],
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
            Column::make('titulo')->title('Título'),
            Column::make('texto')->title('Texto'),
            Column::make('tipo')->title('Tipo'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Avisos_' . date('YmdHis');
    }
}
