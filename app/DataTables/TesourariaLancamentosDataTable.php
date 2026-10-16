<?php

namespace App\DataTables;

use App\Helpers\BootstrapHelper;
use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Services\Apps\TesourariaService;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TesourariaLancamentosDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $saldos = $this->saldosAcumulados();

        return datatables()
            ->eloquent($query)
            ->addColumn('action', function($sql) {
                return view('dashboard.apps.tesouraria.actions', [
                    'route' => 'dashboard.apps.tesouraria',
                    'id' => $sql->id,
                    'comprovante' => $sql->comprovante
                ]);
            })
            ->editColumn('tipo', function($sql) {
                return FormHelper::statusFormatado($sql->tipo, 'Entrada', 'Saída');
            })
            ->editColumn('data_lancamento', function($sql) {
                return Carbon::parse($sql->data_lancamento)->format('d/m/Y');
            })
            ->editColumn('categoria_id', function ($sql) {
                return $this->badgeCategoria($sql);
            })
            ->editColumn('valor', function ($sql) {
                $valor = (float) $sql->getRawOriginal('valor');
                $entrada = (int) $sql->tipo === Lancamento::TIPO_ENTRADA;

                return $entrada
                    ? '<span class="text-success font-weight-bold">' . TesourariaService::formatarMoeda($valor, true) . '</span>'
                    : '<span class="text-danger font-weight-bold">' . TesourariaService::formatarMoeda($valor * -1) . '</span>';
            })
            ->addColumn('saldo_acumulado', function ($sql) use ($saldos) {
                $saldo = $saldos[$sql->id] ?? 0;
                $classe = $saldo < 0 ? 'text-danger' : 'text-muted';

                return '<span class="' . $classe . '">' . TesourariaService::formatarMoeda($saldo) . '</span>';
            })
            ->rawColumns(['action', 'tipo', 'categoria_id', 'valor', 'saldo_acumulado']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Tesouraria\Lancamento $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lancamento $model)
    {
        return $model->newQuery()
            ->with('categoria')
            ->daMinhaInstancia()
            ->when(request()->filled('dt_lancamento'), function ($sql) {
                $datas = explode(' - ', request()->get('dt_lancamento'));
                if (count($datas) !== 2) {
                    return $sql;
                }

                $periodo[0] = Carbon::createFromFormat('d/m/Y', trim($datas[0]));
                $periodo[1] = Carbon::createFromFormat('d/m/Y', trim($datas[1]));
                return $sql->whereBetween('data_lancamento', $periodo);
            })
            ->when(
                request()->filled('mes') && !request()->filled('dt_lancamento'),
                function ($sql) {
                    $mes = request()->get('mes');
                    if (!is_string($mes) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
                        return $sql;
                    }

                    $inicio = Carbon::createFromFormat('!Y-m', $mes)->startOfMonth();
                    $fim = $inicio->copy()->endOfMonth();

                    return $sql->whereBetween('data_lancamento', [
                        $inicio->toDateString(),
                        $fim->toDateString(),
                    ]);
                }
            )
            ->when(request()->filled('tipo'), function ($sql) {
                return $sql->where('tipo', request()->get('tipo'));
            })
            ->when(request()->filled('categoria'), function ($sql) {
                if (request()->get('categoria') === 'sem') {
                    return $sql->whereNull('categoria_id');
                }

                return $sql->where('categoria_id', request()->get('categoria'));
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
            ->setTableId('lancamento-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create')
                    ->text('<i class="fas fa-plus"></i> Novo Lançamento')
                    ->addClass('bg-secondary')
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
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ação'),
            Column::make('data_lancamento')->title('Data Lançamento'),
            Column::make('tipo')->title('Tipo'),
            Column::make('descricao')->title('Descrição'),
            Column::make('categoria_id')->title('Categoria'),
            Column::make('valor')->title('Valor'),
            Column::computed('saldo_acumulado')
                ->title('Saldo acumulado')
                ->orderable(false)
                ->searchable(false),
        ];
    }

    /**
     * Saldo da conta depois de cada lançamento, independente do filtro da página.
     *
     * @return array<string, float>
     */
    private function saldosAcumulados(): array
    {
        $acumulado = 0.0;
        $mapa = [];

        Lancamento::query()
            ->orderBy('data_lancamento')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'valor', 'tipo'])
            ->each(function ($lancamento) use (&$acumulado, &$mapa) {
                $valor = (float) $lancamento->getRawOriginal('valor');

                if ((int) $lancamento->tipo === Lancamento::TIPO_SAIDA) {
                    $valor *= -1;
                }

                $acumulado += $valor;
                $mapa[$lancamento->id] = $acumulado;
            });

        return $mapa;
    }

    /**
     * @param Lancamento $lancamento
     *
     * @return string
     */
    private function badgeCategoria(Lancamento $lancamento): string
    {
        if (is_null($lancamento->categoria_id) || is_null($lancamento->categoria)) {
            return BootstrapHelper::badge('secondary', 'Sem categoria', true);
        }

        return BootstrapHelper::badge('primary', e($lancamento->categoria->nome), true);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'TESOURARIA_' . date('YmdHis');
    }
}
