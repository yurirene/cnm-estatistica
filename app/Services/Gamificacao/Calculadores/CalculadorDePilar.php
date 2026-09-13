<?php

namespace App\Services\Gamificacao\Calculadores;

use App\Services\Gamificacao\DTOs\ContextoInstancia;
use App\Services\Gamificacao\DTOs\ResultadoPilar;

interface CalculadorDePilar
{
    public function calcular(ContextoInstancia $contexto): ResultadoPilar;
}
