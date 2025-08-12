<?php

namespace App\DataTables;

use App\Helpers\FormHelper;
use App\Helpers\BootstrapHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\User;
use App\Services\UserService;
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
                if (!is_null($sql->categoria_id)) {
                    return BootstrapHelper::badge('primary', $sql->categoria->nome, true);
                }
                return BootstrapHelper::badge('info', 'Sem Categoria', true);
            })
            ->rawColumns(['action', 'tipo', 'categoria_id']);
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
            ->daMinhaInstancia()
            ->when(request()->filled('dt_lancamento'), function ($sql) {
                $dataLancamento = request()->get('dt_lancamento');
                $datas = explode(' - ', $dataLancamento);
                $periodo[0] = Carbon::createFromFormat('d/m/Y', $datas[0]);
                $periodo[1] = Carbon::createFromFormat('d/m/Y', $datas[1]);
                return $sql->whereBetween('data_lancamento', $periodo);
            })
            ->when(request()->filled('tipo'), function ($sql) {
                return $sql->where('tipo', request()->get('tipo'));
            })
            ->when(request()->filled('categoria'), function ($sql) {
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
            Column::make('valor')->title('Valor'),
            Column::make('categoria_id')->title('Categoria'),
        ];
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
