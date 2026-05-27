<?php

namespace App\Models;

use App\Enums\ResolucaoOrigem;
use App\Enums\ResolucaoPrioridade;
use App\Enums\ResolucaoStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resolucao extends Model
{
    protected $table = 'resolucoes';

    protected $fillable = [
        'numero',
        'titulo',
        'descricao',
        'origem',
        'status',
        'prioridade',
        'data_aprovacao',
        'prazo_final',
        'responsavel_id',
        'criado_por',
        'anexos',
        'ultimo_alerta_prazo_em',
    ];

    protected $casts = [
        'origem' => ResolucaoOrigem::class,
        'status' => ResolucaoStatus::class,
        'prioridade' => ResolucaoPrioridade::class,
        'data_aprovacao' => 'date',
        'prazo_final' => 'date',
        'anexos' => 'array',
        'ultimo_alerta_prazo_em' => 'date',
    ];

    protected $appends = [
        'esta_atrasado',
    ];

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    protected function estaAtrasado(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status !== ResolucaoStatus::Concluido
                && $this->prazo_final !== null
                && $this->prazo_final->lt(now()->startOfDay())
        );
    }

    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query
            ->where('status', '!=', ResolucaoStatus::Concluido->value)
            ->whereNotNull('prazo_final')
            ->whereDate('prazo_final', '<', now()->toDateString());
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', ResolucaoStatus::Pendente->value);
    }

    public function scopeConcluidas(Builder $query): Builder
    {
        return $query->where('status', ResolucaoStatus::Concluido->value);
    }
}
