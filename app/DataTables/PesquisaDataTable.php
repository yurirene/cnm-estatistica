<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BoostrapHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Pesquisas\Pesquisa;
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
                    'show' => !$this->verificarUsuariosDiretoria(),
                    'delete' => false,
                    'edit' => $this->verificarUsuariosPermitidosParaConfiguracoes($sql),
                    'status' => $this->verificarUsuariosVinculados($sql),
                    'respostas' => $this->verificarUsuariosVinculados($sql),
                    'configuracoes' => $this->verificarUsuariosPermitidosParaConfiguracoes($sql),
                    'relatorio' =>  $this->verificarUsuariosVinculados($sql),
                    'acompanhar' =>  $this->verificarUsuariosDiretoria()
                ]);
            })
            ->addColumn('usuarios', function($sql) {
                return $sql->usuarios->pluck('name')->map(function($item) {
                    return BootstrapHelper::badge('primary', $item, true);
                })->implode(' ');
            })
            ->addColumn('nro_respostas', function($sql) {
                return $sql->respostas->count();
            })
            ->editColumn('status', function($sql) {
                return FormHelper::statusFormatado($sql->status, 'Aberto', 'Fechado');
            })
            ->addColumn('status_minha_resposta', function($sql) {
                $resposta = $sql->respostas()->where('user_id', Auth::id())->count();
                return FormHelper::statusFormatado($resposta, 'Respondido', 'Pendente');
            })
            ->editColumn('instancias', function($sql) {
                return implode(' ', array_map(function($item) {
                    return BootstrapHelper::badge('primary', $item, true);
                }, $sql->instancias));
            })
            ->rawColumns(['status', 'status_minha_resposta', 'usuarios', 'instancias']);
    }

    public function verificarUsuariosVinculados(Pesquisa $pesquisa) : bool
    {
        $usuarios_vinculados = $pesquisa->whereHas('usuarios', function($sql) {
            return $sql->where('users.id', Auth::id());
         })->get()->isNotEmpty();
        if ( $usuarios_vinculados || Auth::user()->admin == true) {
            return true;
        }
        return false;
    }

    public function verificarPefilInstancia() : bool
    {
        return !in_array(Auth::user()->roles->first()->name, User::ROLES_INSTANCIAS);
    }

    public function verificarUsuariosPermitidosParaConfiguracoes() : bool
    {
        return Auth::user()->admin == true;
    }

    public function verificarUsuariosDiretoria() : bool
    {
        return Auth::user()->roles->first()->name == 'diretoria';
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Pesquisa $model)
    {
        return $model->newQuery()->when(in_array(Auth::user()->roles->first()->name, User::ROLES_SECRETARIOS), function($sql) {
            return $sql->whereHas('usuarios', function($q) {
                return $q->where('users.id', Auth::id());
            });
        })
        ->when(in_array(Auth::user()->roles->first()->name, User::ROLES_INSTANCIAS), function($sql) {
            return $sql->whereJsonContains('instancias', Auth::user()->instancia_formatada)
                ->where('status', true);
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
                    ->setTableId('usuario-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> Nova Pesquisa')
                            ->enabled(auth()->user()->canAtLeast(['dashboard.pesquisas.create']))
                            ->addClass(!$this->verificarPefilInstancia() ? 'd-none' : null)
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
            Column::make('nome')->title('Nome'),
            Column::make('status')->title('Status')->visible($this->verificarPefilInstancia()),
            Column::make('status_minha_resposta')->title('Status')->visible(!$this->verificarPefilInstancia()),
            Column::make('nro_respostas')->title('Nº de Respostas')->visible($this->verificarPefilInstancia()),
            Column::make('instancias')->title('Instâncias')->visible($this->verificarPefilInstancia()),
            Column::make('usuarios')->title('Usuários')->visible($this->verificarPefilInstancia()),
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
