<?php

namespace App\DataTables;

use App\Models\Missionario;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MissionariosDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function (Missionario $missionario) {
                return view('includes.actions', [
                    'route' => 'dashboard.adote-missionario',
                    'id' => $missionario->id,
                ]);
            })
            ->addColumn('localizacao', function (Missionario $missionario) {
                $uf = $missionario->estado?->sigla;
                $regiao = $missionario->regiao?->nome;
                $cidade = $missionario->cidade ?: '—';
                $pais = $missionario->pais ?: '';

                if ($uf || $regiao) {
                    return e("{$cidade}" . ($uf ? "/{$uf}" : '') . ($regiao ? " · {$regiao}" : '') . ($pais ? " · {$pais}" : ''));
                }

                return e(trim("{$cidade} · {$pais}", ' ·'));
            })
            ->addColumn('status_adocao', function (Missionario $missionario) {
                if (!$missionario->estaAdotado()) {
                    return '<span class="badge badge-success">Disponível</span>';
                }

                $tipo = e($missionario->adotante_tipo);
                $nome = e($missionario->adotante_nome);
                $data = $missionario->adotado_em?->format('d/m/Y H:i') ?? '';

                return '<span class="badge badge-primary">Adotado</span> '
                    . "<small class=\"d-block text-muted\">{$tipo}: {$nome}"
                    . ($data ? " · {$data}" : '')
                    . '</small>';
            })
            ->addColumn('agencia', function (Missionario $missionario) {
                $agencia = $missionario->agencia;

                if ($agencia === 'JMN') {
                    return '<span class="badge badge-info">JMN</span>';
                }

                if ($agencia === 'APMT') {
                    return '<span class="badge badge-secondary">APMT</span>';
                }

                return '<span class="text-muted">—</span>';
            })
            ->addColumn('foto', function (Missionario $missionario) {
                if (!$missionario->foto_url) {
                    return '<span class="text-muted">—</span>';
                }

                return '<img src="' . e($missionario->foto_url) . '" alt="' . e($missionario->nome) . '" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">';
            })
            ->rawColumns(['action', 'status_adocao', 'agencia', 'foto']);
    }

    public function query(Missionario $model)
    {
        return $model->newQuery()
            ->with(['estado', 'regiao', 'sinodal', 'federacao'])
            ->select('missionarios.*')
            ->when(
                request()->filled('regiao_id'),
                fn ($q) => $q->where('regiao_id', request('regiao_id'))
            )
            ->daAgencia(in_array(request('agencia'), ['jmn', 'apmt'], true) ? request('agencia') : null);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('missionarios-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', null, [
                'regiao_id' => '$("#filtro-regiao").val()',
                'agencia' => '$("#filtro-agencia").val()',
            ])
            ->dom('Bfrtip')
            ->pageLength(20)
            ->orderBy(2, 'asc')
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
                ->width(60)
                ->addClass('text-center')
                ->title('Ação'),
            Column::make('foto')->title('Foto')->orderable(false)->searchable(false),
            Column::make('nome')->title('Nome'),
            Column::computed('agencia')->title('Agência')->orderable(false)->searchable(false),
            Column::computed('localizacao')->title('Localização')->orderable(false)->searchable(false),
            Column::make('pais')->title('País'),
            Column::computed('status_adocao')->title('Adoção')->orderable(false)->searchable(false),
            Column::make('email')->title('E-mail'),
            Column::make('whatsapp')->title('WhatsApp'),
        ];
    }

    protected function filename(): string
    {
        return 'Missionarios_' . date('YmdHis');
    }
}
