<?php

namespace App\Models\Congresso;

use App\Casts\FileCast;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegadoCongresso extends Model
{
    use HasFactory, GenericTrait;

    protected $table = 'congresso_delegados';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'path_credencial' => FileCast::class,
        'credencial' => 'boolean',
        'suplente' => 'boolean',
        'pago' => 'boolean'
    ];

    public string $caminho = 'public/congresso/delegados';

    // Status constants
    public const STATUS_REJEITADA = -1;
    public const STATUS_PENDENTE = 0;
    public const STATUS_EM_ANALISE = 1;
    public const STATUS_CONFIRMADA = 2;

    //status pagamento
    public const STATUS_PAGAMENTO_CONFIRMADO = ['paid', 'confirmed'];

    public const STATUS_LIST = [
        self::STATUS_REJEITADA => 'Rejeitada',
        self::STATUS_PENDENTE => 'Pendente',
        self::STATUS_EM_ANALISE => 'Em análise',
        self::STATUS_CONFIRMADA => 'Confirmada'
    ];

    public const STATUS_LABELS = [
        self::STATUS_REJEITADA => '<span class="badge bg-danger">Rejeitada</span>',
        self::STATUS_PENDENTE => '<span class="badge bg-warning">Pendente</span>',
        self::STATUS_EM_ANALISE => '<span class="badge bg-primary">Em análise</span>',
        self::STATUS_CONFIRMADA => '<span class="badge bg-success">Confirmada</span>'
    ];

    public function getStatusFormatadoAttribute(): string
    {
        return self::STATUS_LABELS[$this->status];
    }

    public function reuniao(): BelongsTo
    {
        return $this->belongsTo(ReuniaoCongresso::class, 'reuniao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function getInstanciaAttribute(): string
    {
        if ($this->sinodal_id) {
            return $this->sinodal->nome;
        }

        if ($this->federacao_id) {
            return $this->federacao->nome;
        }

        if ($this->local_id) {
            return $this->local->nome;
        }

        return 'Congresso Nacional';
    }

    public function getRegiaoAttribute(): string
    {
        if ($this->sinodal_id) {
            return $this->sinodal->regiao->nome;
        }

        if ($this->federacao_id) {
            return $this->federacao->regiao->nome;
        }

        if ($this->local_id) {
            return $this->local->federacao->regiao->nome;
        }

        return 'Nacional';
    }
}
