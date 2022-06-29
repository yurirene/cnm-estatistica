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
                    'route' => 'dashboard.comprovante_aci',
                    'id' => $sql->id,
                    'confirmar' => auth()->user()->roles->pluck('name') == 'tesouraria',
                    'edit' => false,
                    'delete' => false,
                    'abrir' => $this->getPath($sql->path)
                ]);
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Presente', 'Pendente');
            })
            ->editColumn('sinodal_id', function($sql) {
                return $sql->sinodal->sigla;
            })
            ->rawColumns(['status']);
    }

    public function getPath(string $path)
    {
        return $path;
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
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('ano')->title('ano'),
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
