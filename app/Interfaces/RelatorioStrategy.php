<?php

namespace App\Interfaces;

interface RelatorioStrategy
{
    public function getDados(): array;
    public function gerar(string $tipo);
    public function gerarCsv(array $dados);
    public function gerarPdf(array $dados);
}