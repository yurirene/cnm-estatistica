<?php

namespace App\Console\Commands;

use App\Services\ResolucaoService;
use Illuminate\Console\Command;

class AlertarPrazosResolucoes extends Command
{
    protected $signature = 'resolucoes:alertar-prazos';

    protected $description = 'Envia alertas de prazos de resoluções para o Telegram dos responsáveis';

    public function handle(): int
    {
        $enviados = ResolucaoService::alertarPrazos();

        $this->info("Alertas enviados: {$enviados}");

        return self::SUCCESS;
    }
}
