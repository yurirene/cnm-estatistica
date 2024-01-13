<?php

namespace App\Factories;

use Exception;

class DetalhamentoFactory
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
        return "\App\DataTables\Detalhamento\\{$nomeClasse}Datatable";
    }

    private static function validaClasse(string $pathClasse): void
    {
        if (!class_exists($pathClasse)) {
            throw new Exception("Classe {$pathClasse} não encontrada.", 500);
        }
    }
}
