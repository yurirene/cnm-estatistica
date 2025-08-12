<?php

namespace App\DataTables\ComissaoExecutiva;

use App\Helpers\BootstrapHelper;
use App\Models\ComissaoExecutiva\DelegadoComissaoExecutiva;
use App\Models\ComissaoExecutiva\DocumentoRecebido;
use App\Models\Sinodal;
use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DelegadosDataTable extends DataTable
{

    protected bool $perfilSinodal = false;
    protected string $reuniao;
    protected bool $perfilExecutiva = false;
    protected bool $perfilDiretoria = false;

    public function __construct(string $reuniao)
    {
        $this->perfilSinodal = !in_array(auth()->user()->role->name, [
            User::ROLE_SEC_EXECUTIVA,
            User::ROLE_DIRETORIA
        ]);
        $this->perfilExecutiva = auth()->user()->role->name == User::ROLE_SEC_EXECUTIVA;
        $this->perfilDiretoria = auth()->user()->role->name == User::ROLE_DIRETORIA;
        $this->reuniao = $reuniao;
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
            ->addColumn('action', function ($sql) {
                return view('dashboard.comissao-executiva.actions-delegado', [
                    'id' => $sql->id
                ]);
            })
            ->editColumn('status', function ($sql) {
                return $sql->status_formatado;
            })
            ->editColumn('pago', function ($sql) {
                $cor = $sql->pago ? 'success' : 'danger';
                $texto = $sql->pago ? 'Pago' : 'Pendente';

                return BootstrapHelper::badge($cor, $texto, true);
            })
            ->editColumn('credencial', function ($sql) {
                $cor = $sql->credencial ? 'success' : 'danger';
                $texto = $sql->credencial ? 'Entregue' : 'Pendente';

                return BootstrapHelper::badge($cor, $texto, true);
            })
            ->editColumn('updated_at', function ($sql) {
                return $sql->updated_at->format('d/m/Y H:i:s');
            })
            ->editColumn('sinodal_id', function ($sql) {
                return $sql->sinodal->nome;
            })
            ->editColumn('created_at', function ($sql) {
                return $sql->created_at->format('d/m/Y H:i:s');
            })
            ->rawColumns(['status', 'credencial', 'pago']);
    }

    /**
     * Get query source of dataTable.
     *
     */
    public function query(DelegadoComissaoExecutiva $model)
    {
        return $model->newQuery()
            ->where('reuniao_id', $this->reuniao);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('ce-credencial-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('dashboard.comissao-executiva.delegados-datatable', ['reuniao' => $this->reuniao]))
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(1, 'asc')
            ->buttons([])
            ->parameters([
                "buttons" => [],
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
            Column::make('nome')->title('Nome'),
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('status')->title('Status'),
            Column::make('credencial')->title('Credencial'),
            Column::make('pago')->title('Pago'),
            Column::make('created_at')->title('Enviado em'),
            Column::make('updated_at')->title('Atualizado em'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'CE_' . date('YmdHis');
    }
}
