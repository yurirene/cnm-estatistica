<?php

namespace App\Exports;

use App\Models\FormularioSinodal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BaseDadosFormularioExport implements FromView
{

    public $cabecalho;
    public $colunas;
    public $ano;

    public function __construct(array $cabecalho, array $colunas, int $ano)
    {
        $this->cabecalho = $cabecalho;
        $this->colunas = $colunas;
        $this->ano = $ano;
    }

    public function view(): View
    {
        return view('export.base_dados_formulario_sinodal', [
            'dados' => FormularioSinodal::where('ano_referencia',$this->ano)->get(),
            'cabecalho' => $this->headings(),
            'coluna_por_grupo' => $this->cabecalho
        ]);
    }

    public function headings(): array
    {
        $cabecalhos = [];
        foreach ($this->cabecalho as $cabecalho => $campos) {
            if (!is_array($campos)) {
                $cabecalhos[] = $campos;
                continue;
            }
            $this->separarCabecalho($cabecalhos,$cabecalho, count($this->cabecalho[$cabecalho]));
        }
        return [
           $cabecalhos,
           $this->colunas,
        ];
    }

    public function separarCabecalho(array &$array_cabacalhos, string $cabecalho, int $total) : array
    {
        $i = 0;
        for($i; $i < $total; $i++) {
            if ($i==0) {
                $array_cabacalhos[] = $cabecalho;
                continue;
            }
            $array_cabacalhos[] = '';
        }
        return $array_cabacalhos;
    }
}
