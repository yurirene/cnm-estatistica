<?php 

namespace App\Strategies\PesquisaGrafico;

use Exception;

abstract class AbstractGrafico
{
    protected const PALETA = [
        "#03071E", "#370617", "#6A040F", "#9D0208", "#D00000", "#DC2F02", "#E85D04", "#F48C06", "#FAA307", "#FFBA08"
    ];



    protected static function getPaleta(array $dados) : array 
    {
        try {
            $retorno = array();
            $i = 0;
            $cores = array();
            while ($i < count($dados['dados'])) {
                $selecionado = array_rand(self::PALETA);
                if (in_array(self::PALETA[$selecionado], $cores)) {
                    continue;
                } else {
                    $cores[] = self::PALETA[$selecionado];
                    $i++;
                }
            }
            return $cores;
        } catch (\Throwable $th) {
            throw new Exception("Erro no getPaleta", 1);
            
        }
    }

    protected static function gerarPorcentagem(array $quantidades) : array
    {
        try {
            $total = array_sum($quantidades);
            $retorno = array();
            if ($total == 0) {
                return [0];
            }
            foreach ($quantidades as $quantidade) {
                $divisao = ($quantidade * 100) / $total;
                $retorno[] = round($divisao, 2);
            }
            return $retorno;
        } catch (\Throwable $th) {
            throw new Exception("Erro no gerarPorcentagem", 1);
        }
    }

    protected static function gerarPorcentagemComTotal(array $quantidades, $valor) : array
    {
        try {
            
            $total = array_sum($quantidades);
            if ($total == 0) {
                return [0];
            }
            return ($valor * 100) / $total;
        } catch (\Throwable $th) {
            throw new Exception("Erro no gerarPorcentagem", 1);
        }
    }

}
