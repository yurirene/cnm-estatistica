<?php

namespace App\Services\Gamificacao\Regras;

use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Carbon\Carbon;

class CalendarioGamificacao
{
    public function ciclo(): string
    {
        return (string) GamificacaoConfiguracaoService::get('ciclo', '2026-2030');
    }

    public function anoGamificacao(int $ano): int
    {
        $inicio = GamificacaoConfiguracaoService::getInt('ciclo_inicio', 2026);

        return max(1, $ano - $inicio + 1);
    }

    public function prazoEstatistica(int $anoReferencia): Carbon
    {
        $prazo = GamificacaoConfiguracaoService::get('prazo_estatistica', ['mes' => 3, 'dia' => 1]);

        return Carbon::create($anoReferencia + 1, (int) $prazo['mes'], (int) $prazo['dia'])->startOfDay();
    }

    public function prazoSpeedRun(int $anoReferencia): Carbon
    {
        $prazo = GamificacaoConfiguracaoService::get('prazo_speed_run', ['mes' => 1, 'dia' => 10]);

        return Carbon::create($anoReferencia + 1, (int) $prazo['mes'], (int) $prazo['dia'])->endOfDay();
    }

    public function anoFechado(int $anoReferencia, ?Carbon $agora = null): bool
    {
        $agora ??= Carbon::now();

        return $agora->greaterThanOrEqualTo($this->prazoEstatistica($anoReferencia));
    }

    public function tetoCiclo(NaturezaInstancia $natureza): int
    {
        return $natureza->tetoCiclo();
    }
}
