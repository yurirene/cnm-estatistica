<?php

namespace App\DataTables;

use App\Helpers\BootstrapHelper;
use App\Models\AcessoExterno;
use App\Models\ComprovanteACI;
use App\Models\FormularioSinodal;
use App\Models\User;
use App\Services\ComprovanteAciService;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ComprovanteAciDataTable extends DataTable
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
                $tesouraria = auth()->user()->role->name == 'tesouraria';

                return view('includes.actions', [
                    'route' => 'dashboard.comprovante-aci',
                    'id' => $sql->id,
                    'status' => $tesouraria,
                    'metaAtingida' => $tesouraria,
                    'jaMetaAtingida' => (int) $sql->status === ComprovanteACI::STATUS_META_ATINGIDA,
                    'edit' => false,
                    'delete' => false,
                    'abrir' => $sql->path
                ]);
            })
            ->editColumn('status', function($sql) {
                $status = (int) $sql->status;
                $label = ComprovanteACI::STATUS_LABELS[$status] ?? ComprovanteACI::STATUS_LABELS[ComprovanteACI::STATUS_PENDENTE];

                return BootstrapHelper::badge($label['cor'], $label['texto']);
            })
            ->editColumn('sinodal_id', function($sql) {
                return $sql->sinodal->sigla;
            })
            ->addColumn('valor_informado', function($sql) {
                return $this->valorInformado($sql);
            })
            ->editColumn('created_at', function($sql) {
                return $sql->created_at->format('d/m/Y H:i:s');
            })
            ->addColumn('valor_previsto', function($sql) {
                return $this->valorPrevisto($sql);
            })
            ->addColumn('valor_necessario', function ($sql) {
                $result = ComprovanteAciService::totalizadorAciNecessaria($sql->sinodal_id, (int) $sql->ano);
                $titulo = htmlspecialchars(
                    'Referente a: ' . ($result['total_socios'] ?? 0) . ' sócios',
                    ENT_QUOTES,
                    'UTF-8'
                );

                return $result['valor']
                    . ' <span class="comprovante-aci-hint"'
                    . ' data-toggle="tooltip" data-bs-toggle="tooltip"'
                    . ' data-placement="top" data-bs-placement="top"'
                    . ' data-container="body" data-bs-container="body"'
                    . ' title="' . $titulo . '"'
                    . ' style="cursor:pointer;display:inline-block;padding:0 4px">'
                    . '<i class="fas fa-info-circle"></i>'
                    . '</span>';
            })
            ->rawColumns(['status', 'valor_necessario']);
    }

    public function valorInformado($sql)
    {
        $formulario = FormularioSinodal::where('sinodal_id', $sql->sinodal_id)
            ->where('ano_referencia', $sql->ano)->first();
        if (is_null($formulario)) {
            return 'Formulário não respondido';
        }
        return isset($formulario['aci']['valor_repassado'])
            ? 'R$' . $formulario['aci']['valor_repassado']
            : 'Não Informado';
    }

    public function valorPrevisto($sql)
    {
        return ComprovanteAciService::valorPrevisto($sql->sinodal_id, (int) $sql->ano);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ComprovanteACI $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComprovanteACI $model)
    {
        $filtro = json_decode(request()->get('filtro'), true);
        return $model->newQuery()->meusComprovantes()
            ->when(!empty($filtro['ano_referencia']), function($sql) use ($filtro) {
                $sql->where('ano', $filtro['ano_referencia']);
            })
            ->when(!empty($filtro['data_criacao']), function($sql) use ($filtro) {
                $datas = explode(' - ', $filtro['data_criacao']);
                $periodo[0] = Carbon::createFromFormat('d/m/Y', $datas[0]);
                $periodo[1] = Carbon::createFromFormat('d/m/Y', $datas[1]);
                return $sql->whereBetween('created_at', $periodo);
            })
            ->when(!empty($filtro['status']) && $filtro['status'] != 'T', function($sql) use ($filtro) {
                if ($filtro['status'] == 'C') {
                    return $sql->where('status', ComprovanteACI::STATUS_APROVADO);
                }
                if ($filtro['status'] == 'M') {
                    return $sql->where('status', ComprovanteACI::STATUS_META_ATINGIDA);
                }

                return $sql->where('status', ComprovanteACI::STATUS_PENDENTE);
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
            ->setTableId('comprovantes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtipl')
            ->orderBy(7)
            ->parameters([
                "language" => [
                    "url" => "/vendor/datatables/portugues.json"
                ],
                'buttons' => [],
                'responsive' => true,
                'drawCallback' => 'function() { if (window.initComprovanteAciTooltips) { window.initComprovanteAciTooltips(); } }',
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
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('ano')->title('Ano Referência'),
            Column::make('valor_informado')->title('Valor Informado'),
            Column::make('valor_previsto')->title('Valor Previsto'),
            Column::make('valor_necessario')->title('Valor Mínimo'),
            Column::make('status')->title('Status'),
            Column::make('created_at')->title('Cadastrado em'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Comprovante_ACI_' . date('YmdHis');
    }


    /**
     * Retorna os dados dos filtros da tabela
     *
     * @return array
     */
    public function filtros(): array
    {
        if (!$this->verificaSeUsarioTesouraria()) {
            return [];
        }
        $status = [
            'T' => 'Todos',
            'P' => 'Pendentes',
            'C' => 'Confirmados',
            'M' => 'Meta atingida',
        ];
        $anosCadastrados = ComprovanteAciService::getAnosCadastrados();
        return [
            'data_criacao' => true,
            'ano_referencia' => ["" => "Todos"] + $anosCadastrados,
            'status' => $status
        ];
    }

    /**
     * Verifica se o usário é do perfil tesouraria
     *
     * @return boolean
     */
    public function verificaSeUsarioTesouraria(): bool
    {
        return auth()->user()->role->name == User::ROLE_TESOURARIA;
    }
}
