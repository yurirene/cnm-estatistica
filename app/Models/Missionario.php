<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Missionario extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'missionarios';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'adotado_em' => 'datetime',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class, 'regiao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function estaAdotado(): bool
    {
        return !is_null($this->sinodal_id) || !is_null($this->federacao_id);
    }

    public function getAdotanteNomeAttribute(): ?string
    {
        if ($this->sinodal_id) {
            return $this->sinodal?->nome;
        }

        if ($this->federacao_id) {
            return $this->federacao?->nome;
        }

        return null;
    }

    public function getAdotanteTipoAttribute(): ?string
    {
        if ($this->sinodal_id) {
            return 'Sinodal';
        }

        if ($this->federacao_id) {
            return 'Federação';
        }

        return null;
    }

    public function getLocalizacaoAttribute(): string
    {
        $uf = $this->estado?->sigla ?? '';
        $regiao = $this->regiao?->nome ?? '';
        $cidade = $this->cidade ?? '';

        return trim("{$cidade} - {$uf} ({$regiao}), {$this->pais}", ' -,()');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }

        if (Str::startsWith($this->foto, ['http://', 'https://'])) {
            return $this->foto;
        }

        return asset($this->foto);
    }

    public function fotoEhExterna(): bool
    {
        return !empty($this->foto) && Str::startsWith($this->foto, ['http://', 'https://']);
    }

    public function scopeDaAgencia($query, ?string $agencia)
    {
        return $query
            ->when($agencia === 'jmn', fn ($q) => $q->whereNotNull('jmn_id'))
            ->when($agencia === 'apmt', fn ($q) => $q->whereNotNull('apmt_id'));
    }

    public function getAgenciaAttribute(): ?string
    {
        if ($this->jmn_id) {
            return 'JMN';
        }

        if ($this->apmt_id) {
            return 'APMT';
        }

        return null;
    }

    public function scopeDisponiveis($query)
    {
        return $query->whereNull('sinodal_id')->whereNull('federacao_id');
    }

    public function scopeDaInstancia($query, string $campo, string $id)
    {
        return $query->where($campo, $id);
    }
}
