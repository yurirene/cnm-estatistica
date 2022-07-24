<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BoostrapHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Pesquisa;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PesquisaDataTable extends DataTable
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
                    'route' => 'dashboard.pesquisas',
                    'id' => $sql->id,
                    'show' => true,
                    'delete' => false,
                    'status' => true,
                    'respostas' => true,
                    'configuracoes' => true,
                    'relatorio' => true
                ]);
            })
            ->addColumn('usuarios', function($sql) {
                return implode(', ',$sql->usuarios->pluck('name')->toArray());
            })
            ->addColumn('nro_respostas', function($sql) {
                return $sql->respostas->count();
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Aberto', 'Fechado');
            })
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Pesquisa $model)
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
                    ->setTableId('usuario-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Pesquisa')
                            ->enabled(auth()->user()->canAtLeast(['dashboard.pesquisas.create']))
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
        $colunas = [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('nome')->title('Nome'),
            Column::make('status')->title('Status'),
            Column::make('nro_respostas')->title('Nº de Respostas'),
            Column::make('usuarios')->title('Usuários'),
        ];
        return $colunas;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PESQUISA_' . date('YmdHis');
    }
}
