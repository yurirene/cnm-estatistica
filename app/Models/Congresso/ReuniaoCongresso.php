<?php

namespace App\Models\Congresso;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReuniaoCongresso extends Model
{
    use HasFactory, GenericTrait;

    protected $table = 'congresso_reunioes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'aberto' => 'boolean',
        'diretoria' => 'boolean',
        'relatorio_estatistico' => 'boolean',
        'status' => 'integer'
    ];

    // Status constants
    public const STATUS_INATIVO = 0;
    public const STATUS_ATIVO = 1;
    public const STATUS_ENCERRADO = 2;

    public const STATUS_LIST = [
        self::STATUS_INATIVO => 'Inativo',
        self::STATUS_ATIVO => 'Ativo',
        self::STATUS_ENCERRADO => 'Encerrado'
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoRecebidoCongresso::class, 'reuniao_id');
    }

    public function delegados(): HasMany
    {
        return $this->hasMany(DelegadoCongresso::class, 'reuniao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Sinodal::class, 'sinodal_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Federacao::class, 'federacao_id');
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Local::class, 'local_id');
    }

    public function isCongressoNacional(): bool
    {
        return is_null($this->sinodal_id) && is_null($this->federacao_id);
    }

    public function getTipoAttribute(): string
    {
        if ($this->isCongressoNacional()) {
            return 'Congresso Nacional';
        }

        if ($this->sinodal_id) {
            return 'Congresso Sinodal';
        }

        if ($this->federacao_id) {
            return 'Congresso de Federação';
        }

        return 'Congresso Local';
    }
}
