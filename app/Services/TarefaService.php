<?php

namespace App\Services;

use App\Enums\TarefaPeriodoNotificacao;
use App\Enums\TarefaStatus;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TarefaService
{
    public static function queryParaUsuario(?User $user = null)
    {
        $user = $user ?? Auth::user();

        return Tarefa::query()
            ->with('usuario:id,name,email,telegram_chat_id')
            ->where('user_id', $user->id)
            ->latest();
    }

    public static function estatisticas(?User $user = null): array
    {
        $base = self::queryParaUsuario($user);
        $hoje = now()->toDateString();

        $stats = (clone $base)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as concluidas', [TarefaStatus::Concluido->value])
            ->selectRaw(
                'SUM(CASE WHEN status = ? AND prazo_final IS NOT NULL AND prazo_final < ? THEN 1 ELSE 0 END) as atrasadas',
                [TarefaStatus::Pendente->value, $hoje]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pendentes',
                [TarefaStatus::Pendente->value]
            )
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'pendentes' => (int) ($stats->pendentes ?? 0),
            'concluidas' => (int) ($stats->concluidas ?? 0),
            'atrasadas' => (int) ($stats->atrasadas ?? 0),
        ];
    }

    public static function criar(array $dados, ?User $user = null): Tarefa
    {
        $user = $user ?? Auth::user();

        $tarefa = Tarefa::create([
            'titulo' => $dados['titulo'],
            'descricao' => $dados['descricao'] ?? null,
            'prazo_final' => $dados['prazo_final'] ?? null,
            'periodo_notificacao' => $dados['periodo_notificacao'] ?? TarefaPeriodoNotificacao::Semanal->value,
            'status' => $dados['status'] ?? TarefaStatus::Pendente->value,
            'user_id' => $user->id,
        ]);

        self::notificarTarefa($tarefa->fresh('usuario'), true);

        return $tarefa;
    }

    public static function atualizar(Tarefa $tarefa, array $dados): Tarefa
    {
        if (
            isset($dados['status'])
            && $dados['status'] === TarefaStatus::Pendente->value
            && $tarefa->status === TarefaStatus::Concluido
        ) {
            $dados['ultimo_alerta_em'] = null;
        }

        $tarefa->update($dados);

        return $tarefa->fresh('usuario');
    }

    public static function excluir(Tarefa $tarefa): void
    {
        $tarefa->delete();
    }

    public static function encerrar(Tarefa $tarefa): Tarefa
    {
        $tarefa->update(['status' => TarefaStatus::Concluido->value]);

        return $tarefa->fresh();
    }

    public static function reabrir(Tarefa $tarefa): Tarefa
    {
        $tarefa->update([
            'status' => TarefaStatus::Pendente->value,
            'ultimo_alerta_em' => null,
        ]);

        return $tarefa->fresh();
    }

    public static function enviarNotificacoes(): int
    {
        $enviados = 0;

        $tarefas = Tarefa::query()
            ->pendentes()
            ->with('usuario')
            ->get()
            ->filter(fn (Tarefa $tarefa) => $tarefa->deveNotificar());

        foreach ($tarefas as $tarefa) {
            if (self::notificarTarefa($tarefa)) {
                $enviados++;
            }
        }

        return $enviados;
    }

    public static function notificarTarefa(Tarefa $tarefa, bool $nova = false): bool
    {
        $usuario = $tarefa->usuario;

        if (empty($usuario?->telegram_chat_id)) {
            return false;
        }

        if ($tarefa->status !== TarefaStatus::Pendente) {
            return false;
        }

        $mensagem = self::montarMensagem($tarefa, $nova);

        if (!TelegramService::sendMessage($usuario->telegram_chat_id, $mensagem)) {
            return false;
        }

        $tarefa->update(['ultimo_alerta_em' => now()]);

        return true;
    }

    public static function opcoesPeriodo(): array
    {
        return collect(TarefaPeriodoNotificacao::cases())
            ->mapWithKeys(fn (TarefaPeriodoNotificacao $p) => [$p->value => $p->label()])
            ->all();
    }

    public static function opcoesStatus(): array
    {
        return [
            TarefaStatus::Pendente->value => 'Pendente',
            TarefaStatus::Concluido->value => 'Concluído',
        ];
    }

    protected static function montarMensagem(Tarefa $tarefa, bool $nova = false): string
    {
        $tipo = $nova ? '📋 <b>Nova tarefa</b>' : '🔔 <b>Lembrete de tarefa</b>';

        if ($tarefa->esta_atrasada) {
            $tipo = '⚠️ <b>Tarefa atrasada</b>';
        }

        $mensagem = "{$tipo}\n<b>{$tarefa->titulo}</b>";

        if ($tarefa->descricao) {
            $mensagem .= "\n" . strip_tags($tarefa->descricao);
        }

        if ($tarefa->prazo_final) {
            $mensagem .= "\nPrazo: " . $tarefa->prazo_final->format('d/m/Y');
        }

        $mensagem .= "\nNotificações: a cada " . self::labelPeriodoCurto($tarefa->periodo_notificacao);

        return $mensagem;
    }

    protected static function labelPeriodoCurto(TarefaPeriodoNotificacao $periodo): string
    {
        return match ($periodo) {
            TarefaPeriodoNotificacao::Diario => '1 dia',
            TarefaPeriodoNotificacao::ACada2Dias => '2 dias',
            TarefaPeriodoNotificacao::ACada3Dias => '3 dias',
            TarefaPeriodoNotificacao::Semanal => '7 dias',
            TarefaPeriodoNotificacao::Quinzenal => '15 dias',
            TarefaPeriodoNotificacao::Mensal => '30 dias',
        };
    }

    public static function pertenceAoUsuario(Tarefa $tarefa, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        return $tarefa->user_id === $user->id;
    }
}
