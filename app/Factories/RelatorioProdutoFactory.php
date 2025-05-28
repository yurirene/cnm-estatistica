<?php

namespace App\Factories;

use Exception;

class RelatorioProdutoFactory
{
    public static function make(string $nomeClasse)
    {
        $nome = str_replace('_', '', ucwords($nomeClasse, '_'));
        $classe = self::getClasse($nome);
        self::validaClasse($classe);

        return new $classe;
    }

    private static function getClasse(string $nomeClasse): string
    {
        return "\App\Strategies\Produtos\\{$nomeClasse}Strategy";
    }

    private static function validaClasse(string $pathClasse): void
    {
        if (!class_exists($pathClasse)) {
            throw new Exception("Classe {$pathClasse} não encontrada.", 500);
        }
    }
}
