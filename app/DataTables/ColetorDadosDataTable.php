<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Models\ColetorDados;
use App\Services\ColetorDadosService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ColetorDadosDataTable extends DataTable
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
            ->addColumn('action', function ($sql) {
                return view('dashboard.coletor-dados.actions', [
                    'route' => 'dashboard.coletor-dados',
                    'id' => $sql->id,
                    'resposta' => !empty($sql->resposta['raw'])
                        ? json_encode(ColetorDadosService::formatarRespostaParaVisualizacao($sql->resposta['raw']))
                        : ''
                ]);
            })
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Respondido', 'Sem Resposta');
            })
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ColetorDados $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ColetorDados $model)
    {
        return $model->newQuery()
            ->where('local_id', auth()->user()->local_id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $rotaDelete = route('coletor-dados.restaurar', ['local' => auth()->user()->local_id]);

        return $this->builder()
                    ->setTableId('coletor-dados-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->pageLength(20)
                    ->orderBy(1, 'asc')
                    ->parameters([
                        "buttons" => [
                            [
                                'text' => '<i class="fas fa-plus"></i> Novo Registro',
                                'action' => "function() { $('#modal-create').modal('show'); }"
                            ],
                            [
                                'text' => '<i class="fas fa-print"></i> Baixar Lista',
                                'extend' => 'csv'
                            ],
                            [
                                'text' => '<i class="fas fa-trash"></i> Apagar tudo',
                                'action' => "function() { alertConfirmar('{$rotaDelete}', 'Deseja apagar todos os formulários, até os respondidos? (Faça isso se quiser coletar tudo novamente)') }",
                                'className' => 'btn-danger'
                            ]
                        ],
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
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('id')->title('Código'),
            Column::make('status')->title('Status'),
            Column::make('ano')->title('Ano'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Formularios' . date('YmdHis');
    }
}
