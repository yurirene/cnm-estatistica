<?php

namespace App\Interfaces;

use App\Models\Pesquisas\Pesquisa;
use Illuminate\Database\Eloquent\Model;

interface PesquisaGraficoStrategy
{

    public function handle(Model $pesquisa, string $campo, string $chave) : array;

    public static function getDados(Model $pesquisa, string $campo, string $chave) : array;

    public static function formatarDados(array $dados, string $tipo_dado) : array;

    public static function renderizarHtml(array $dados, string $tipo) : string;

    public static function script(array $dados, string $tipo) : string;
}
