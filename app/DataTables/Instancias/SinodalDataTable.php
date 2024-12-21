<?php

namespace App\DataTables\Instancias;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Sinodal;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SinodalDataTable extends DataTable
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
                    'route' => 'dashboard.sinodais',
                    'id' => $sql->id,
                    'show' => true,
                    'delete' => $sql->dadosFederacaoLocal['nro_federacoes'] > 0 ? false : true
                ]);
            })
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Ativo', 'Inativo');
            })
            ->editColumn('regiao_id', function ($sql) {
                return $sql->dadosFederacaoLocal['regiao'];
            })
            ->addColumn('nro_federacoes', function ($sql) {
                return $sql->dadosFederacaoLocal['nro_federacoes'];
            })
            ->addColumn('nro_locais', function ($sql) {
                return $sql->dadosFederacaoLocal['nro_locais'];
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AcessoExterno $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Sinodal $model)
    {
        return $model->newQuery()->query()
            ->when(request()->filled('organizadas'), function ($sql) {
                return $sql->where('status', true);
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
                    ->setTableId('sinodais-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Sinodal')
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
            Column::make('nro_federacoes')->title('Nº Federações')->orderable(false),
            Column::make('nro_locais')->title('Nº UMPs Locais')->orderable(false),
            Column::make('status')->title('Status'),
            Column::make('regiao_id')->title('Região'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Sinodais_' . date('YmdHis');
    }
}
