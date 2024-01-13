<?php

namespace App\DataTables\Detalhamento;

use App\Helpers\FormHelper;
use App\Models\AcessoExterno;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Formularios\FormularioFederacaoService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class IgrejasDatatable extends DataTable
{

    public string $titulo = 'Igrejas';
    public string $subtitulo = 'Lista de Igrejas';
    public ?int $anoReferencia = null;
    public function __construct()
    {
        if (request()->filled('organizadas')) {
            $this->titulo = 'UMPs Locais';
            $this->subtitulo = 'Lista de UMPs Locais Organizadas';
        }
        $this->anoReferencia = FormularioFederacaoService::getAnoReferencia();
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
            ->editColumn('federacao_id', function ($sql) {
                return $sql->federacao->nome;
            })
            ->editColumn('sinodal_id', function ($sql) {
                return $sql->sinodal->sigla;
            })
            ->editColumn('status', function ($sql) {
                return FormHelper::statusFormatado($sql->status, 'Organizada', 'Não Organizada');
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
     * @param \App\Models\Local $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Local $model)
    {
        $filtro = json_decode(request()->get('filtro'), true);
        return $model->newQuery()
            ->when($this->verificarUsuariosDiretoria(), function ($sql) {
                return $sql->daMinhaRegiao();
            })
            ->when(
                !empty($filtro['status'])
                && $filtro['status'] != 'T'
                && !request()->filled('organizadas'),
                function ($sql) use ($filtro) {
                    return $sql->where('status', $filtro['status'] == 'A');
                }
            )
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
    public function verificarUsuariosDiretoria() : bool
    {
        return auth()->user()->roles->first()->name == User::ROLE_DIRETORIA;
    }


    /**
     * Verifica se usuário é da sinodal
     *
     * @return boolean
     */
    public function verificarUsuariosSinodal() : bool
    {
        return auth()->user()->roles->first()->name == User::ROLE_SINODAL;
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
            Column::make('federacao_id')->title('Federação'),
            Column::make('sinodal_id')->title('Sinodal'),
            Column::make('estado_id')->title('Estado'),
            Column::make('status')->title('Status'),
            Column::make('relatorio')->title('Relatório')->searchable(false)
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
        $estados = Estado::daMinhaRegiao()->get()->pluck('nome', 'id')->toArray();
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
