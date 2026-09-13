<?php

namespace App\Models\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Services\Gamificacao\Enums\OrigemAuditoria;
use App\Services\Gamificacao\Enums\Pilar;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    use GenericTrait;

    protected $table = 'gamificacao_auditoria';

    protected $guarded = ['id'];

    protected $casts = [
        'antes' => 'array',
        'depois' => 'array',
        'origem' => OrigemAuditoria::class,
        'pilar' => Pilar::class,
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new GamificacaoException('A auditoria da gamificação é somente inclusão.');
        });

        static::deleting(function () {
            throw new GamificacaoException('A auditoria da gamificação não pode ser apagada.');
        });
    }

    public function placar(): BelongsTo
    {
        return $this->belongsTo(Placar::class, 'placar_id');
    }
}
