<?php

namespace App\Factories;

use App\Interfaces\PesquisaGraficoStrategy;
use Exception;

class PesquisaGraficoFactory
{
    public static function make(string $nomeClasse) : PesquisaGraficoStrategy
    {
        $nome = str_replace('_', '', ucwords($nomeClasse, '_'));
        $classe = self::getClasse($nome);
        self::validaClasse($classe);

        return (new $classe);
    }

    private static function getClasse(string $nomeClasse): string
    {
        return "\App\Strategies\PesquisaGrafico\\{$nomeClasse}Strategy";
    }

    private static function validaClasse(string $pathClasse): void
    {
        if (!class_exists($pathClasse)) {
            throw new Exception("Classe {$pathClasse} não encontrada.", 500);
        }
    }
}
