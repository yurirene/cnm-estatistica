<?php

namespace App\DataTables\Instancias;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Federacao;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FederacaoDataTable extends DataTable
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
                return view('includes.actions', [
                    'route' => 'dashboard.federacoes',
                    'id' => $sql->id,
                    'show' => true,
                    'delete' => $sql->dadosDatatable['nro_locais'] > 0 ? false : true
                ]);
            })
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Ativo', 'Inativo');
            })
            ->editColumn('regiao_id', function ($sql) {
                return $sql->dadosDatatable['regiao'];
            })
            ->addColumn('estatistica', function ($sql) {
                return $sql->dadosDatatable['estatistica'];
            })
            ->editColumn('estado_id', function ($sql) {
                return $sql->dadosDatatable['estado'];
            })
            ->editColumn('sinodal_id', function ($sql) {
                return $sql->dadosDatatable['sigla_sinodal'];
            })
            ->addColumn('nro_umps', function ($sql) {
                return $sql->dadosDatatable['nro_locais'];
            })
            ->addColumn('diretoria', function ($sql) {
                return $sql->diretoria
                    ? $sql->diretoria->updated_at->format('d/m/y')
                    : 'Sem Diretoria';
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Federacao $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Federacao $model)
    {
        if (Auth::user()->admin == true) {
            return $model->newQuery();
        }
        return $model->newQuery()->minhaSinodal();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('federacoes-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Federação')
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
            Column::make('nome')->title('Nome'),
            Column::make('sigla')->title('Sigla'),
            Column::make('nro_umps')->title('Nº UMPs')->orderable(false),
            Column::make('estatistica')->title('Estatística')->orderable(false),
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('estado_id')->title('Estado'),
            Column::make('status')->title('Status'),
            Column::make('diretoria')->title('Att. Diretoria'),
            Column::make('regiao_id')->title('Região'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Federacao_' . date('YmdHis');
    }
}
