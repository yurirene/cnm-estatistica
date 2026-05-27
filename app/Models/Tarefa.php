<?php

namespace App\Models;

use App\Enums\TarefaPeriodoNotificacao;
use App\Enums\TarefaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    protected $fillable = [
        'titulo',
        'descricao',
        'prazo_final',
        'periodo_notificacao',
        'status',
        'user_id',
        'ultimo_alerta_em',
    ];

    protected $casts = [
        'prazo_final' => 'date',
        'periodo_notificacao' => TarefaPeriodoNotificacao::class,
        'status' => TarefaStatus::class,
        'ultimo_alerta_em' => 'datetime',
    ];

    protected $appends = [
        'esta_atrasada',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function estaAtrasada(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === TarefaStatus::Pendente
                && $this->prazo_final !== null
                && $this->prazo_final->lt(now()->startOfDay())
        );
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', TarefaStatus::Pendente->value);
    }

    public function deveNotificar(): bool
    {
        if ($this->status !== TarefaStatus::Pendente) {
            return false;
        }

        if ($this->ultimo_alerta_em === null) {
            return true;
        }

        return $this->ultimo_alerta_em
            ->addDays($this->periodo_notificacao->dias())
            ->lte(now());
    }
}
