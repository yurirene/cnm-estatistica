<?php

namespace App\Console\Commands;

use App\Services\TarefaService;
use Illuminate\Console\Command;

class AlertarTarefas extends Command
{
    protected $signature = 'tarefas:alertar';

    protected $description = 'Envia lembretes periódicos de tarefas via Telegram';

    public function handle(): int
    {
        $enviados = TarefaService::enviarNotificacoes();

        $this->info("Notificações enviadas: {$enviados}");

        return self::SUCCESS;
    }
}
