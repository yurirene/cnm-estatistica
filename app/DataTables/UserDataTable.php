<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BoostrapHelper;
use App\Helpers\BootstrapHelper;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Ativo', 'Inativo');
            })
            ->addColumn('perfil', function($sql) {
                $roles = '';
                foreach ($sql->roles as $role) {
                    $roles .= BootstrapHelper::badge('primary', $role->description, true);
                }
                return $roles;
            })
            ->addColumn('administrando', function($sql) {
                $administrando = '';
                foreach (UserService::getAdministrados($sql->id) as $adm) {
                    $administrando .= BootstrapHelper::badge('primary', $adm['texto'], true);
                }
                return $administrando;
            })
            ->rawColumns(['status', 'perfil', 'administrando']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
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
                    ->setTableId('usuario-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(2)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Novo Usuário')->enabled(auth()->user()->canAtLeast(['dashboard.usuarios.create']))
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
        $colunas = [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('name')->title('Nome'),
            Column::make('email')->title('E-mail'),
            Column::make('perfil')->title('Perfil'),
            Column::make('administrando')->title('Administrando'),
            Column::make('status')->title('Status'),
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
        return 'USUARIOS_' . date('YmdHis');
    }
}
