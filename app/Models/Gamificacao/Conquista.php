<?php

namespace App\Models\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Models\User;
use App\Scopes\GamificacaoInstanciaScope;
use App\Services\Gamificacao\Enums\TipoConquista;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conquista extends Model
{
    use GenericTrait;

    protected $table = 'gamificacao_conquistas';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'pontos',
    ];

    protected $casts = [
        'tipo' => TipoConquista::class,
        'ano_referencia' => 'integer',
        'pontos' => 'integer',
        'concedido_em' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new GamificacaoInstanciaScope());

        static::creating(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Conquistas só podem ser gravadas pelo serviço de atualização.');
            }
        });

        static::updating(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Conquistas só podem ser alteradas pelo serviço de atualização.');
            }
        });

        static::deleting(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Conquistas não podem ser removidas fora do serviço de atualização.');
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

    public function concedente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concedido_por');
    }
}
