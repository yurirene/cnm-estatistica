<?php

namespace App\DataTables;

use App\Models\Digesto;
use App\Services\DigestoService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DigestoDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($sql) {
                $editar = route('dashboard.digestos.edit', $sql->id);
                $arquivo = $sql->path ?: '#';

                return '<div class="digesto-row-actions">'
                    . '<a href="' . e($editar) . '" class="digesto-icon-btn" title="Editar"><i class="fas fa-pen"></i></a>'
                    . '<a href="' . e($arquivo) . '" target="_blank" class="digesto-icon-btn" title="Abrir arquivo"><i class="far fa-file-alt"></i></a>'
                    . '</div>';
            })
            ->editColumn('tipo_reuniao_id', function ($sql) {
                $nome = $sql->tipo->nome ?? '';
                if ($nome === '') {
                    return '—';
                }

                return '<span class="digesto-meeting-badge">' . e($nome) . '</span>';
            })
            ->editColumn('tipo_documento', function ($sql) {
                return $this->celulaOpcional(DigestoService::labelTipoDocumento($sql->tipo_documento, true));
            })
            ->editColumn('numero_documento', function ($sql) {
                return $this->celulaOpcional($sql->numero_documento);
            })
            ->editColumn('comissao', function ($sql) {
                return filled($sql->comissao) ? e($sql->comissao) : '—';
            })
            ->addColumn('status', function ($sql) {
                if (DigestoService::estaIncompleto($sql)) {
                    return '<span class="digesto-status incomplete"><span class="d"></span>Incompleto</span>';
                }

                return '<span class="digesto-status complete"><span class="d"></span>Completo</span>';
            })
            ->addColumn('DT_RowClass', function ($sql) {
                return DigestoService::estaIncompleto($sql) ? 'digesto-admin-incomplete' : '';
            })
            ->rawColumns([
                'action',
                'tipo_reuniao_id',
                'tipo_documento',
                'numero_documento',
                'comissao',
                'status',
            ]);
    }

    public function query(Digesto $model)
    {
        return $model->newQuery()
            ->with('tipo')
            ->when(request()->filled('tipo_reuniao'), function ($query) {
                return $query->where('tipo_reuniao_id', request('tipo_reuniao'));
            })
            ->when(request()->filled('ano'), function ($query) {
                return $query->where('ano', request('ano'));
            })
            ->when(request('status') === 'incompleto', function ($query) {
                return DigestoService::aplicarIncompleto($query);
            })
            ->when(request('status') === 'completo', function ($query) {
                return $query->whereNotNull('tipo_documento')
                    ->where('tipo_documento', '!=', '')
                    ->whereNotNull('numero_documento')
                    ->where('numero_documento', '!=', '');
            });
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('digesto-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax('', null, [
                        'tipo_reuniao' => '$("#digesto-filtro-reuniao").val()',
                        'ano' => '$("#digesto-filtro-ano").val()',
                        'status' => '$("#digesto-filtro-status").val()',
                    ])
                    ->dom('rtip')
                    ->orderBy(1)
                    ->parameters([
                        'language' => [
                            'url' => '/vendor/datatables/portugues.json',
                        ],
                        'pageLength' => 10,
                    ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center')
                ->title(''),
            Column::make('titulo')->title('Título'),
            Column::make('ano')->title('Ano')->width(70),
            Column::make('tipo_reuniao_id')->title('Reunião')->searchable(false),
            Column::make('tipo_documento')->title('Tipo')->searchable(false),
            Column::make('numero_documento')->title('Nº Doc.'),
            Column::make('comissao')->title('Comissão'),
            Column::computed('status')
                ->title('Status')
                ->searchable(false)
                ->orderable(false)
        ];
    }

    protected function filename(): string
    {
        return 'DIGESTOS_' . date('YmdHis');
    }

    private function celulaOpcional(?string $valor): string
    {
        if (! filled($valor)) {
            return '<span class="digesto-missing">faltando</span>';
        }

        return e($valor);
    }
}
