<?php

namespace App\DataTables\AuxilioPmf;

use App\Helpers\BootstrapHelper;
use App\Models\AuxilioPmf\AuxilioPmfProposta;
use App\Services\AuxilioPmfService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PropostasDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('sinodal', function ($sql) {
                return $sql->sinodal->nome ?? '-';
            })
            ->addColumn('regiao', function ($sql) {
                return $sql->sinodal->regiao->nome ?? '-';
            })
            ->addColumn('proposta_pdf', function ($sql) {
                $url = AuxilioPmfService::urlArquivo($sql->path_proposta);

                if (!$url) {
                    return '-';
                }

                return BootstrapHelper::buttonLink('primary', 'Ver PDF', $url, true);
            })
            ->addColumn('relatorio_pdf', function ($sql) {
                $url = AuxilioPmfService::urlArquivo($sql->path_relatorio);

                if (!$url) {
                    return '-';
                }

                return BootstrapHelper::buttonLink('info', 'Ver PDF', $url, true);
            })
            ->editColumn('status', function ($sql) {
                $label = AuxilioPmfProposta::STATUS_LABELS[$sql->status]
                    ?? AuxilioPmfProposta::STATUS_LABELS[AuxilioPmfProposta::STATUS_PENDENTE];

                return BootstrapHelper::badge($label['cor'], $label['texto']);
            })
            ->addColumn('action', function ($sql) {
                if ($sql->status !== AuxilioPmfProposta::STATUS_PENDENTE) {
                    return '-';
                }

                if (empty($sql->getRawOriginal('path_proposta'))) {
                    return '-';
                }

                $aprovar = '<a href="' . route('dashboard.auxilio-pmf.proposta.aprovar', $sql->id) . '"'
                    . ' class="btn btn-sm btn-success"'
                    . ' onclick="return confirm(\'Aprovar esta proposta?\')">Aprovar</a>';
                $rejeitar = '<a href="' . route('dashboard.auxilio-pmf.proposta.rejeitar', $sql->id) . '"'
                    . ' class="btn btn-sm btn-danger"'
                    . ' onclick="return confirm(\'Rejeitar esta proposta? A sinodal não poderá enviar outra neste ano.\')">Rejeitar</a>';

                return $aprovar . ' ' . $rejeitar;
            })
            ->rawColumns(['status', 'proposta_pdf', 'relatorio_pdf', 'action']);
    }

    public function query(AuxilioPmfProposta $model)
    {
        $editalId = request()->get('edital_id');

        return $model->newQuery()
            ->with(['sinodal.regiao', 'edital'])
            ->when($editalId, fn ($q) => $q->where('edital_id', $editalId))
            ->when(!$editalId, function ($q) {
                $edital = AuxilioPmfService::getEditalVigente();
                if ($edital) {
                    return $q->where('edital_id', $edital->id);
                }

                return $q->whereRaw('1 = 0');
            });
    }

    public function html()
    {
        $edital = AuxilioPmfService::getEditalVigente();

        return $this->builder()
            ->setTableId('auxilio-pmf-propostas-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('dashboard.auxilio-pmf.propostas-datatable'), null, [
                'edital_id' => $edital?->id,
            ])
            ->dom('Bfrtipl')
            ->orderBy(1)
            ->parameters([
                'language' => [
                    'url' => '/vendor/datatables/portugues.json',
                ],
                'buttons' => [],
                'responsive' => true,
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(160)
                ->addClass('text-center')
                ->title('Ação'),
            Column::make('sinodal')->title('Sinodal')->orderable(false)->searchable(false),
            Column::make('regiao')->title('Região')->orderable(false)->searchable(false),
            Column::computed('proposta_pdf')->title('Proposta')->orderable(false)->searchable(false),
            Column::computed('relatorio_pdf')->title('Relatório')->orderable(false)->searchable(false),
            Column::make('status')->title('Status'),
        ];
    }

    protected function filename(): string
    {
        return 'Auxilio_PMF_Propostas_' . date('YmdHis');
    }
}
