<?php

namespace App\DataTables;

use App\Helpers\BootstrapHelper;
use App\Models\Demanda;
use App\Models\DemandaItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MinhasDemandasDataTable extends DataTable
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
            ->editColumn('nivel', function($sql) {
                return $sql->nivel_formatado;
            })
            ->editColumn('status', function($sql) {
                return BootstrapHelper::badge(DemandaItem::STATUS_LABELS[$sql->status] , DemandaItem::STATUS[$sql->status]);
            })
            ->rawColumns(['nivel', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Demanda $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DemandaItem $model)
    {
        return $model->newQuery()
            ->where('user_id', Auth::id())
            ->when(request()->filled('demanda'), function($sql) {
                return $sql->where('demanda_id', request()->get('demanda'));
            })
            ->when(request()->filled('status'), function($sql) {
                return $sql->where('status', request()->get('status'));
            })
            ->when(request()->filled('nivel'), function($sql) {
                return $sql->where('nivel', request()->get('nivel'));
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
                    ->setTableId('minhas-demandas-item-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
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
            Column::make('nivel')->title('Nível')->searchable(false),
            Column::make('demanda')->title('Demanda'),
            Column::make('status')->title('Status')->searchable(false),
            Column::make('origem')->title('Origem'),
            Column::make('documento')->title('Documento'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'DEMANDAS_' . date('YmdHis');
    }
}
