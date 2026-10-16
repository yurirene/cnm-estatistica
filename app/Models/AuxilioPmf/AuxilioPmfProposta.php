<?php

namespace App\Models\AuxilioPmf;

use App\Casts\FileCast;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuxilioPmfProposta extends Model
{
    use GenericTrait;

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADA = 'aprovada';
    public const STATUS_REJEITADA = 'rejeitada';

    public const STATUS_LABELS = [
        self::STATUS_PENDENTE => ['texto' => 'Pendente', 'cor' => 'warning'],
        self::STATUS_APROVADA => ['texto' => 'Aprovada', 'cor' => 'success'],
        self::STATUS_REJEITADA => ['texto' => 'Rejeitada', 'cor' => 'danger'],
    ];

    public string $caminho = 'public/auxilio-pmf';

    protected $table = 'auxilio_pmf_propostas';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'path_proposta' => FileCast::class,
        'path_relatorio' => FileCast::class,
    ];

    public function edital(): BelongsTo
    {
        return $this->belongsTo(AuxilioPmfEdital::class, 'edital_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function isAprovada(): bool
    {
        return $this->status === self::STATUS_APROVADA;
    }

    public function isRejeitada(): bool
    {
        return $this->status === self::STATUS_REJEITADA;
    }
}
