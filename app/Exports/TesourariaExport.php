<?php

namespace App\Exports;

use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\Pesquisas\Pesquisa;
use App\Services\Apps\TesourariaService;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TesourariaExport implements WithMultipleSheets
{
    use Exportable;

    protected int $ano;
    protected int $comComprovante;

    public function __construct(
        int $ano,
        bool $comComprovante
    ) {
        $this->ano = $ano;
        $this->comComprovante = $comComprovante;
    }


    public function sheets(): array
    {
        $sheets = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $dataInicio = Carbon::createFromFormat('d/m/Y', "01/{$mes}/{$this->ano}");
            $dataFim = clone $dataInicio;
            $sheets[] = new TesourariaPorMesExport(
                $dataInicio,
                $dataFim->lastOfMonth(),
                strtoupper($dataInicio->shortMonthName),
                $this->comComprovante
            );
        }

        $sheets[] = new TesourariaBalanceteExport($this->ano);

        return $sheets;
    }

}
