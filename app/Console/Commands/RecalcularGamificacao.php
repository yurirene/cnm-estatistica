<?php

namespace App\Console\Commands;

use App\Services\Gamificacao\Enums\NaturezaInstancia;
use App\Services\Gamificacao\GamificacaoAtualizacaoService;
use Illuminate\Console\Command;

class RecalcularGamificacao extends Command
{
    protected $signature = 'gamificacao:recalcular {ano?} {--natureza=} {--forcar}';

    protected $description = 'Recalcula os placares da gamificação do ciclo PE 2026–2030';

    public function handle(GamificacaoAtualizacaoService $service): int
    {
        $ano = $this->argument('ano') ? (int) $this->argument('ano') : null;
        $naturezaOpcao = $this->option('natureza');
        $natureza = $naturezaOpcao ? NaturezaInstancia::from($naturezaOpcao) : null;
        $forcar = (bool) $this->option('forcar');

        $total = $service->recalcularTodos($ano, $natureza, $forcar);
        $this->info("Placares recalculados: {$total}");

        return self::SUCCESS;
    }
}
