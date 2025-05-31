<?php

namespace App\Services\Produtos;

use App\Factories\RelatorioProdutoFactory;

class RelatorioService
{
    public static function gerarRelatorio(array $request)
    {
        $data = explode(' - ', $request['periodo']);
        $relatorio = RelatorioProdutoFactory::make(
            $request['tipo'],
            $data[0],
            $data[1]
        );

        return $relatorio->gerar($request['formato']);
    }
}