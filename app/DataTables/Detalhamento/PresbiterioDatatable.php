<?php

namespace App\DataTables\Detalhamento;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioFederacaoService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PresbiterioDatatable extends DataTable
{

    public string $titulo = 'Presbitério';
    public string $subtitulo = 'Lista de Presbitérios';
    public ?int $anoReferencia = null;

    public function __construct()
    {
        if (request()->filled('organizadas')) {
            $this->titulo = 'Federações';
            $this->subtitulo = 'Lista de Federações Organizadas';
        }
        $this->anoReferencia = EstatisticaService::getAnoReferencia();
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
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Ativo', 'Inativo');
            })
            ->editColumn('estado_id', function ($sql) {
                return $sql->estado->nome;
            })
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Organizada', 'Não Organizada');
            })
            ->editColumn('nro_igrejas', function ($sql) {
                return $sql->locais->count();
            })
            ->editColumn('nro_umps', function ($sql) {
                return $sql->locais()->where('status', true)->count();
            })
            ->editColumn('relatorio', function ($sql) {
                return FormHelper::statusFormatado(
                    $sql->relatorios->where('ano_referencia', $this->anoReferencia)->count(),
                    'Entregue',
                    'Pendente'
                );
            })
            ->rawColumns(['status', 'relatorio']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Federacao $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Federacao $model)
    {
        $filtro = json_decode(request()->get('filtro'), true);
        return $model->newQuery()
            ->when($this->verificarUsuariosDiretoria(), function ($sql) {
                return $sql->daMinhaRegiao();
            })
            ->when($this->verificarUsuariosSinodal(), function ($sql) {
                return $sql->minhaSinodal();
            })
            ->when(
                !empty($filtro['status'])
                && $filtro['status'] != 'T'
                && !request()->filled('organizadas'),
                function ($sql) use ($filtro){
                    return $sql->where('status', $filtro['status'] == 'A');
                }
            )
            ->when(
                !empty($filtro['estados']), function ($sql) use ($filtro){
                return $sql->whereIn('estado_id', $filtro['estados'])->orderBy('estado_id');
            })
            ->when(
                !empty($filtro['estados']), function ($sql) use ($filtro){
                return $sql->whereIn('estado_id', $filtro['estados'])->orderBy('estado_id');
            })
            ->when(request()->filled('organizadas'), function ($sql) {
                return $sql->where('status', true);
            })
            ->orderBy('estado_id', 'asc')
            ->orderBy('status', 'desc');
    }


    /**
     * Verifica se usuário é da diretoria
     *
     * @return boolean
     */
    public function verificarUsuariosDiretoria(): bool
    {
        return auth()->user()->role->name == User::ROLE_DIRETORIA;
    }


    /**
     * Verifica se usuário é da sinodal
     *
     * @return boolean
     */
    public function verificarUsuariosSinodal(): bool
    {
        return auth()->user()->role->name == User::ROLE_SINODAL;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('detalhamento-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtipl')
            ->orderBy(0, 'asc')
            ->buttons(
                Button::make('print')->text('<i class="fas fa-print"></i> Imprimir')
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
        return [
            Column::make('nome')->title('Nome'),
            Column::make('sigla')->title('Sigla'),
            Column::make('estado_id')->title('Estado'),
            Column::make('status')->title('Status'),
            Column::make('nro_igrejas')->title('Nº Igrejas')->orderable(false),
            Column::make('nro_umps')->title('Nº UMPs')->orderable(false),
            Column::make('relatorio')->title('Relatório')->orderable(false)
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Detalhamento_' . date('YmdHis');
    }

    /**
     * Retorna os dados dos filtros da tabela
     *
     * @return array
     */
    public function filtros(): array
    {
        $estados = null;

        if (!$this->verificarUsuariosSinodal()) {
            $estados = Estado::daMinhaRegiao()->get()->pluck('nome', 'id')->toArray();
        }
        $status = [
            'T' => 'Todos',
            'A' => 'Organizada',
            'I' => 'Não Organizada'
        ];
        if (request()->filled('organizadas')) {
            $status = ['A' => 'Organizada'];
        }
        return [
            'estados' => $estados,
            'status' => $status
        ];
    }
}
