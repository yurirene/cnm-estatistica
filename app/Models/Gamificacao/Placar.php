<?php

namespace App\Models\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Scopes\GamificacaoInstanciaScope;
use App\Services\Gamificacao\Enums\Liga;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Placar extends Model
{
    use GenericTrait;

    protected $table = 'gamificacao_placares';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'pontos_ano',
        'pontos_ciclo',
        'posicao_liga',
        'total_na_liga',
        'desconto_projetado',
        'liga',
        'socios_ativos',
        'fechado_em',
        'calculado_em',
    ];

    protected $casts = [
        'liga' => Liga::class,
        'ano_referencia' => 'integer',
        'socios_ativos' => 'integer',
        'pontos_ano' => 'integer',
        'pontos_ciclo' => 'integer',
        'posicao_liga' => 'integer',
        'total_na_liga' => 'integer',
        'desconto_projetado' => 'integer',
        'fechado_em' => 'datetime',
        'calculado_em' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new GamificacaoInstanciaScope());

        static::creating(function (self $model) {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('O placar só pode ser criado pelo serviço de atualização.');
            }
        });

        static::updating(function (self $model) {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('O placar só pode ser alterado pelo serviço de atualização.');
            }
        });

        static::deleting(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('O placar não pode ser removido fora do serviço de atualização.');
            }
        });
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function pilares(): HasMany
    {
        return $this->hasMany(PilarResultado::class, 'placar_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class, 'placar_id');
    }

    public function ehSinodal(): bool
    {
        return $this->sinodal_id !== null;
    }

    public function estaFechado(): bool
    {
        return $this->fechado_em !== null;
    }
}
