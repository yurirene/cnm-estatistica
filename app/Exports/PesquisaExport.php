<?php

namespace App\Exports;

use App\Models\Pesquisas\Pesquisa;
use Exception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PesquisaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $pesquisa;
    protected $colunas;

    public function __construct(Pesquisa $pesquisa)
    {
        $this->pesquisa = $pesquisa;
        $this->colunas = $this->getColunasExport();
    }

    public function collection()
    {
        $respostas = $this->pesquisa->respostas;
        $retorno = [];
        $colunas_permitidas = data_get($this->colunas, '*.campo');
        $i = 0;
        foreach ($respostas as $resposta) {
            foreach ($colunas_permitidas as $coluna) {
                $valor = $resposta->resposta[$coluna];
                if (is_array($valor)) {
                    $valor = $this->getOpcoesLabel($coluna, $valor);
                }
                $retorno[$i][] = $valor;
            }
            $i++;
        }
        return collect($retorno);
    }
    public function headings(): array
    {
        return data_get($this->colunas, '*.label');
    }

    public function getOpcoesLabel(string $coluna, array $valores) : string
    {
        try {

        $referencias = $this->pesquisa->referencias;
        $valores_marcados = array();
        foreach ($referencias as $referencia) {
            foreach ($referencia as $informacoes) {
                if ($informacoes['campo'] != $coluna) {
                    continue;
                }
                foreach ($informacoes['valores'] as $valor_campo) {
                    if (in_array($valor_campo['value'], $valores)) {
                        $valores_marcados[] = $valor_campo['label'];
                    }
                }
            }
        }
        return implode(", ", $valores_marcados);
        } catch (\Throwable $th) {
            throw new Exception("Error buscando label", 1);

        }

    }

    public function getColunasExport() : array
    {
        $configuracoes = $this->pesquisa->configuracao->configuracao;
        $retorno = array();
        foreach ($configuracoes as $configuracao) {
            if ($configuracao['exportar']) {
                $retorno[] = [
                    'campo' => $configuracao['campo'],
                    'label' => $configuracao['label']
                ];
            }
        }
        return $retorno;
    }
}
