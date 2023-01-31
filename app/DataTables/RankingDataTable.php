<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Ranking;
use App\Models\Sinodal;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RankingDataTable extends DataTable
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
                return view('dashboard.apps.actions', [
                    'sinodal_id' => $sql->sinodal_id,
                ]);
            })
            ->editColumn('nome', function ($sql) {
                return $sql->sinodal->nome;
            })
            ->editColumn('apps', function ($sql) {
                $apps = $sql->sinodal->apps->pluck('nome')->toArray();
                return  empty($apps) ? 'Sem apps' : $apps;
            })
            ->rawColumns([]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AcessoExterno $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ranking $model)
    {
        return $model->newQuery()
            ->when(request()->has('ano_referencia'), function ($sql) {
                return $sql->Where('ano_referencia', request('ano_referencia'));
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
                    ->setTableId('ranking-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->pageLength(20)
                    ->orderBy(1, 'asc')
                    ->parameters([
                        "buttons" => [],
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
            Column::make('posicao')->title('Posição'),
            Column::make('nome')->title('Sinodal'),
            Column::make('apps')->title('Apps Liberados'),
            Column::make('ano_referencia')->title('Ano Referência'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Ranking_' . date('YmdHis');
    }
}
