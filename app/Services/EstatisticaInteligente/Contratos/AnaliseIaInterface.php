<?php

namespace App\Services\EstatisticaInteligente\Contratos;

use App\Services\EstatisticaInteligente\DTOs\AnaliseIaResult;

interface AnaliseIaInterface
{
    public function analisar(string $prompt, array $contexto): AnaliseIaResult;
}
