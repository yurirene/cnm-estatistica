<?php

namespace App\DataTables\ComissaoExecutiva;

use App\Helpers\BootstrapHelper;
use App\Models\ComissaoExecutiva\DocumentoRecebido;
use App\Models\Sinodal;
use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DocumentoRecebidoDataTable extends DataTable
{
    protected ?string $reuniao = null;
    protected bool $perfilSinodal = false;
    protected bool $perfilExecutiva = false;
    protected bool $perfilDiretoria = false;

    public function __construct(?string $reuniao = null)
    {
        $this->reuniao = $reuniao;
        $this->perfilSinodal = !in_array(auth()->user()->role->name, [
            User::ROLE_SEC_EXECUTIVA,
            User::ROLE_DIRETORIA
        ]);
        $this->perfilExecutiva = auth()->user()->role->name == User::ROLE_SEC_EXECUTIVA;
        $this->perfilDiretoria = auth()->user()->role->name == User::ROLE_DIRETORIA;
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
                return view('dashboard.comissao-executiva.actions-doc', [
                    'id' => $sql->id,
                    'url' => $sql->path,
                    'confirmar' => [
                        'permissao' => $this->perfilExecutiva,
                        'status' => $sql->status
                    ],
                    'delete' => $this->perfilSinodal
                        && $sql->status != DocumentoRecebido::STATUS_DOCUMENTO_RECEBIDO,
                ]);
            })
            ->editColumn('status', function ($sql) {
                $status = DocumentoRecebido::STATUS_DOCUMENTO[$sql->status];

                return BootstrapHelper::badge('info', $status, true);
            })
            ->editColumn('updated_at', function ($sql) {
                return $sql->updated_at->format('d/m/Y H:i:s');
            })
            ->editColumn('created_at', function ($sql) {
                return $sql->created_at->format('d/m/Y H:i:s');
            })
            ->editColumn('sinodal_id', function ($sql) {
                return $sql->sinodal->nome;
            })
            ->editColumn('tipo', function ($sql) {
                $tipo = DocumentoRecebido::TIPOS_DOCUMENTOS[$sql->tipo];

                return BootstrapHelper::badge('info', $tipo, true);
            })
            ->addColumn('reuniao', function ($sql) {
                return "{$sql->reuniao->local} - {$sql->reuniao->ano}";
            })
            ->rawColumns(['status', 'tipo']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ComissaoExecutiva\DocumentRecebido $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DocumentoRecebido $model)
    {
        $reuniao = $this->reuniao;

        return $model->newQuery()
            ->when(
                !is_null($reuniao),
                function ($sql) use ($reuniao)
                {
                    return $sql->where('reuniao_id', $reuniao)
                        ->where('tipo', '!=', DocumentoRecebido::TIPO_CREDENCIAL_SINODAL);
                }
            )
            ->when(
                $this->perfilDiretoria,
                function ($sql)
                {
                    $sinodais = $sinodais = Sinodal::where('regiao_id', auth()->user()->regiao_id)->pluck('id');
                    return $sql->whereIn('sinodal_id', $sinodais);
                }
            )
            ->when(
                $this->perfilSinodal,
                function ($sql)
                {
                    return $sql->where('sinodal_id', auth()->user()->sinodal_id);
                }
            );
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('ce-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(5, 'desc')
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
            Column::make('titulo')->title('Título/Nome'),
            Column::make('tipo')->title('Tipo'),
            Column::make('sinodal_id')->title('Sinodal')->orderable(false),
            Column::make('status')->title('Status'),
            Column::make('reuniao')->title('Reunião'),
            Column::make('created_at')->title('Enviado em'),
            Column::make('updated_at')->title('Atualizado em'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'CE_' . date('YmdHis');
    }
}
