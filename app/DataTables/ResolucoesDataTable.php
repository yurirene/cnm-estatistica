<?php

namespace App\DataTables;

use App\Enums\ResolucaoStatus;
use App\Models\Resolucao;
use App\Services\ResolucaoService;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ResolucoesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function (Resolucao $resolucao) {
                return view('dashboard.secretaria-executiva.resolucoes.actions', [
                    'resolucao' => $resolucao,
                    'podeGerenciar' => ResolucaoService::isGestor(),
                ]);
            })
            ->editColumn('numero', fn (Resolucao $r) => "<strong>{$r->numero}</strong>")
            ->editColumn('titulo', function (Resolucao $r) {
                $titulo = e(Str::limit($r->titulo, 50));

                if ($r->nao_notificar) {
                    $titulo .= ' <span class="badge badge-secondary" title="Sem notificações Telegram"><i class="fas fa-bell-slash"></i></span>';
                }

                return $titulo;
            })
            ->editColumn('origem', fn (Resolucao $r) => Str::title($r->origem->value))
            ->editColumn('status', function (Resolucao $r) {
                $classe = match ($r->status) {
                    ResolucaoStatus::Concluido => 'success',
                    ResolucaoStatus::Cancelado => 'secondary',
                    ResolucaoStatus::EmAndamento => 'info',
                    default => 'warning',
                };

                return '<span class="badge badge-' . $classe . '">'
                    . ResolucaoService::labelStatus($r->status)
                    . '</span>';
            })
            ->editColumn('prioridade', function (Resolucao $r) {
                $classe = match ($r->prioridade->value) {
                    'alta' => 'danger',
                    'baixa' => 'secondary',
                    default => 'primary',
                };

                return '<span class="badge badge-' . $classe . '">' . Str::title($r->prioridade->value) . '</span>';
            })
            ->editColumn('prazo_final', function (Resolucao $r) {
                if (!$r->prazo_final) {
                    return '<span class="text-muted">—</span>';
                }

                $data = $r->prazo_final->format('d/m/Y');

                if ($r->esta_atrasado) {
                    return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> ' . $data . '</span>';
                }

                $dias = now()->startOfDay()->diffInDays($r->prazo_final, false);

                if ($dias >= 0 && $dias <= ResolucaoService::DIAS_ALERTA_ANTECEDENCIA) {
                    return '<span class="badge badge-warning">' . $data . '</span>';
                }

                return $data;
            })
            ->editColumn('responsavel.name', fn (Resolucao $r) => e($r->responsavel?->name ?? '—'))
            ->rawColumns(['numero', 'titulo', 'status', 'prioridade', 'prazo_final', 'action']);
    }

    public function query(Resolucao $model)
    {
        return ResolucaoService::queryParaUsuario()->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('resolucoes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(1, 'desc')
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
            Column::make('numero')->title('Número'),
            Column::make('titulo')->title('Título'),
            Column::make('origem')->title('Origem'),
            Column::make('status')->title('Status'),
            Column::make('prioridade')->title('Prioridade'),
            Column::make('prazo_final')->title('Prazo final'),
            Column::make('responsavel.name')->title('Responsável'),
        ];
    }

    protected function filename(): string
    {
        return 'Resolucoes_' . date('YmdHis');
    }
}
