<?php

namespace App\Services\Produtos;

use App\Factories\RelatorioProdutoFactory;

class RelatorioService
{
    public static function getRelatorio(array $request)
    {
        $classe = RelatorioProdutoFactory::make($request['relatorio']);
    }
}