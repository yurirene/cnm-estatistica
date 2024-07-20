<?php

namespace App\DataTables;

use App\Models\Estatistica\Ranking;
use App\Services\Estatistica\EstatisticaService;
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
            ->editColumn('checkbox', function($query) {
                return "<input type='checkbox' class='isCheck form-checkbox' name='linhas' value='"
                    . $query->sinodal->id
                    . "'>";
            })
            ->addColumn('action', function ($sql) {
                return view('dashboard.apps.actions', [
                    'sinodal_id' => $sql->sinodal_id,
                ]);
            })
            ->editColumn('nome', function ($sql) {
                return $sql->sinodal->nome;
            })
	        ->editColumn('sigla', function ($sql) {
                return $sql->sinodal->sigla;
            })
            ->editColumn('apps', function ($sql) {
                $apps = $sql->sinodal->apps->pluck('nome')->toArray();
                return  empty($apps) ? 'Sem apps' : $apps;
            })
            ->rawColumns(['checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AcessoExterno $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ranking $model)
    {
        $busca = request()->get('search.value');
        return $model->newQuery()
            ->with('sinodal.regiao')
            ->where('ano_referencia', EstatisticaService::getAnoReferencia())
            ->when(!empty($busca), function ($sql) use ($busca) {
                return $sql->where(function($q) use ($busca) {
                    return $q->where('sinodal.sigla', $busca)
                        ->orWhere('sinodal.nome', $busca);
                });
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
                    ->dom('Bfrtipl')
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
            Column::make('checkbox')->title('<input type="checkbox"  id="checkbox-master" />')
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->searchable(false),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação')->searchable(false),
            Column::make('posicao')->title('Posição')->searchable(false),
            Column::make('sinodal.sigla')->title('Sigla'),
            Column::make('sinodal.nome')->title('Sinodal'),
            Column::make('sinodal.regiao.nome')->title('Regiao'),
            Column::make('apps')->title('Apps Liberados')->searchable(false)->orderable(false),
            Column::make('ano_referencia')->title('Ano Referência')->searchable(false),
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
