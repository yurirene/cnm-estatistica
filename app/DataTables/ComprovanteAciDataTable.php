<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\ComprovanteACI;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ComprovanteAciDataTable extends DataTable
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
                    'route' => 'dashboard.atividades',
                    'id' => $sql->id,
                    'confirmar' => !$sql->status
                ]);
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Presente', 'Pendente');
            })
            ->editColumn('observacao', function($sql) {
                return $sql->observacao;
            })
            ->editColumn('start', function($sql) {
                return $sql->start->format('d/m/Y');
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ComprovanteACI $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComprovanteACI $model)
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
                    ->setTableId('comprovantes-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->parameters([
                        "language" => [
                            "url" => "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                        ],
                        'buttons' => []
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
            Column::make('start')->title('Data'),
            Column::make('observacao')->title('Observação'),
            Column::make('status')->title('Status'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Comprovante_ACI_' . date('YmdHis');
    }
}
