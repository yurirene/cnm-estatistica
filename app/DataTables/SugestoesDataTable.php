<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Sugestao;
use App\Services\UserService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SugestoesDataTable extends DataTable
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
                    'route' => 'dashboard.usuarios',
                    'id' => $sql->id,
                    'delete' => false
                ]);
            })
            ->editColumn('created_at', function($sql) {
                return $sql->created_at->format('d/m/Y');
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Finalizado', 'Aberto');
            })
            ->addColumn('perfil', function($sql) {
                return BootstrapHelper::badge('primary', $sql->usuario->instancia_formatada, true);
            })
            ->addColumn('user_id', function($sql) {
                return $sql->usuario->name;
            })
            ->rawColumns(['status', 'perfil']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Sugestao $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Sugestao $model)
    {
        return $model->newQuery()->query();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('sugestoes-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->buttons([])
                    ->parameters([
                        "language" => [
                            "url" => "/vendor/datatables/portugues.json"
                        ],
                        "buttons" => auth()->user()->admin ? ['print', 'excel'] : []
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
            Column::make('titulo')->title('Título'),
            Column::make('descricao')->title('Descrição')
        ];
        if (auth()->user()->admin) {
            $colunas[] = Column::make('user_id')->title('Usuário');
            $colunas[] = Column::make('perfil')->title('Perfil')->searchable(false);
            $colunas[] = Column::make('status')->title('Status');
        }
        return $colunas;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'SUGESTOES_' . date('YmdHis');
    }
}
