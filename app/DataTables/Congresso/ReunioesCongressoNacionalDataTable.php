<?php

namespace App\DataTables\Congresso;

use App\Helpers\FormHelper;
use App\Models\Congresso\ReuniaoCongresso;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReunioesCongressoNacionalDataTable extends DataTable
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
                return view('dashboard.congresso.actions', [
                    'id' => $sql->id,
                    'delete' => $sql->documentos->isEmpty(),
                    'route' => 'dashboard.congresso-nacional',
                    'podeEditar' => auth()->user()->role->name == 'executiva'
                ]);
            })
            ->editColumn('aberto', function ($sql) {
                if ($sql->status == 1) {
                    return FormHelper::statusFormatado($sql->aberto, 'Doc. Aberto', 'Doc. Fechado');
                }
                return FormHelper::statusFormatado(false, '', 'Encerrado');
            })
            ->rawColumns(['aberto']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Congresso\ReuniaoCongresso $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ReuniaoCongresso $model)
    {
        return $model->newQuery()
            ->whereNull('sinodal_id')
            ->whereNull('federacao_id')
            ->whereNull('local_id')
            ->orderBy('ano', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('congresso-nacional-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(1, 'desc')
            ->buttons([
                Button::make('create')->text('<i class="fas fa-plus"></i> Novo Congresso Nacional')
            ])
            ->parameters([
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
            Column::make('ano')->title('Ano'),
            Column::make('local')->title('Local'),
            Column::make('aberto')->title('Status'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'CongressoNacional_' . date('YmdHis');
    }
}
