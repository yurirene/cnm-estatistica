<?php

namespace App\DataTables;

use App\Helpers\BootstrapHelper;
use App\Models\Demanda;
use App\Models\DemandaItem;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DemandasItemDataTable extends DataTable
{
    public function __construct()
    {
        $demanda_id = explode('/', request()->url());
        $this->demanda_id = end($demanda_id);
    }
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
                return view('dashboard.demandas.actions-item', [
                    'route' => 'dashboard.demandas',
                    'id' => $sql->id,
                    'demanda_id' => $this->demanda_id,
                    'item' => $sql->toJson()
                ]);
            })
            ->editColumn('user_id', function($sql) {
                return $sql->usuario->name;
            })
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
        return $model->newQuery()->where('demanda_id', $this->demanda_id);
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
                    ->orderBy(3)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Demanda')->action('$("#modal-create-item").modal("show");')
                    )
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
            Column::make('nivel')->title('Nível')->searchable(false),
            Column::make('user_id')->title('Responsável')->searchable(false),
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
