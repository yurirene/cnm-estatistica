<?php

namespace App\Console\Commands;

use App\Services\Estatistica\EstatisticaService;
use App\Services\Gamificacao\Regras\JurisdicaoExercicioResolver;
use Illuminate\Console\Command;

class GerarSnapshotVinculosGamificacao extends Command
{
    protected $signature = 'gamificacao:snapshot-vinculos {ano?}';

    protected $description = 'Gera o snapshot de vínculos UMP/Federação/Sinodal de um exercício do Game UMP';

    public function handle(JurisdicaoExercicioResolver $resolver): int
    {
        $ano = $this->argument('ano')
            ? (int) $this->argument('ano')
            : EstatisticaService::getAnoReferencia();

        $total = $resolver->gerarSnapshot($ano);
        $this->info("Snapshot do exercício {$ano}: {$total} vínculos gravados.");

        return self::SUCCESS;
    }
}
