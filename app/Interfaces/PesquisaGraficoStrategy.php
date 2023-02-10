<?php

namespace App\Interfaces;

use App\Models\Pesquisas\Pesquisa;

interface PesquisaGraficoStrategy
{

    public function handle(Pesquisa $pesquisa, string $campo, string $chave) : array;

    public static function getDados(Pesquisa $pesquisa, string $campo, string $chave) : array;

    public static function formatarDados(array $dados, string $tipo_dado) : array;

    public static function renderizarHtml(array $dados, string $tipo) : string;

    public static function script(array $dados, string $tipo) : string;
}
