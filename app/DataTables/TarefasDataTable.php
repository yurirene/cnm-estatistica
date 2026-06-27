<?php

namespace App\DataTables;

use App\Enums\TarefaStatus;
use App\Models\Tarefa;
use App\Services\TarefaService;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TarefasDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function (Tarefa $tarefa) {
                return view('dashboard.tarefas.actions', [
                    'tarefa' => $tarefa,
                ]);
            })
            ->editColumn('titulo', fn (Tarefa $t) => e(Str::limit($t->titulo, 60)))
            ->editColumn('periodo_notificacao', fn (Tarefa $t) => $t->periodo_notificacao->label())
            ->editColumn('status', function (Tarefa $t) {
                $classe = $t->status === TarefaStatus::Concluido ? 'success' : 'warning';

                return '<span class="badge badge-' . $classe . '">'
                    . TarefaService::opcoesStatus()[$t->status->value]
                    . '</span>';
            })
            ->editColumn('prazo_final', function (Tarefa $t) {
                if (!$t->prazo_final) {
                    return '<span class="text-muted">—</span>';
                }

                $data = $t->prazo_final->format('d/m/Y');

                if ($t->esta_atrasada) {
                    return '<span class="badge badge-danger">' . $data . '</span>';
                }

                return $data;
            })
            ->rawColumns(['status', 'prazo_final', 'action']);
    }

    public function query(Tarefa $model)
    {
        return TarefaService::queryParaUsuario();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('tarefas-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(0, 'desc')
            ->parameters([
                'buttons' => [],
                'language' => [
                    'url' => '/vendor/datatables/portugues.json',
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center')
                ->title('Ação'),
            Column::make('titulo')->title('Título'),
            Column::make('periodo_notificacao')->title('Notificação'),
            Column::make('prazo_final')->title('Prazo final'),
            Column::make('status')->title('Status'),
        ];
    }

    protected function filename(): string
    {
        return 'Tarefas_' . date('YmdHis');
    }
}
