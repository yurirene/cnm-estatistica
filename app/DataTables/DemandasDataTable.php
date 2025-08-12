<?php

namespace App\DataTables;

use App\Models\Demanda;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DemandasDataTable extends DataTable
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
                return view('dashboard.demandas.actions', [
                    'route' => 'dashboard.demandas',
                    'id' => $sql->id,
                ]);
            })
            ->editColumn('created_at', function($sql) {
                return Carbon::parse($sql->created_at)->format('d/m/Y H:i:s');
            })
            ->editColumn('path', function($sql) {
                return '<a href="' . $sql->path . '" target="_blank" class="btn btn-light btn-sm"><i class="fas fa-download"></i> Baixar</a>';
            })
            ->addColumn('itens', function($sql) {
                return $sql->itens->count();
            })
            ->rawColumns(['path']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Demanda $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Demanda $model)
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
                    ->setTableId('demandas-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Demanda')
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
            Column::make('titulo')->title('Título'),
            Column::make('path')->title('Arquivo'),
            Column::make('itens')->title('Itens'),
            Column::make('created_at')->title('Criado Em'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'DEMANDAS_' . date('YmdHis');
    }
}
