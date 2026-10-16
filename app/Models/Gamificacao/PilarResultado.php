<?php

namespace App\Models\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Services\Gamificacao\Enums\Pilar;
use App\Services\Gamificacao\Enums\StatusPilar;
use App\Services\Gamificacao\Guards\EscritorPlacar;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilarResultado extends Model
{
    use GenericTrait;

    protected $table = 'gamificacao_pilar_resultados';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'pontos',
        'pontos_maximo',
        'progresso',
        'status',
        'detalhes',
    ];

    protected $casts = [
        'pilar' => Pilar::class,
        'status' => StatusPilar::class,
        'pontos' => 'integer',
        'pontos_maximo' => 'integer',
        'progresso' => 'integer',
        'detalhes' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Resultados de pilar só podem ser gravados pelo serviço de atualização.');
            }
        });

        static::updating(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Resultados de pilar só podem ser alterados pelo serviço de atualização.');
            }
        });

        static::deleting(function () {
            if (! EscritorPlacar::liberado()) {
                throw new GamificacaoException('Resultados de pilar não podem ser removidos fora do serviço de atualização.');
            }
        });
    }

    public function placar(): BelongsTo
    {
        return $this->belongsTo(Placar::class, 'placar_id');
    }
}
