<?php

namespace App\Models\CongressoNacional;

use App\Casts\FileCast;
use App\Models\CongressoReuniao;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegadoCongressoNacional extends Model
{
    use HasFactory, GenericTrait;

    protected $table = 'congresso_nacional_delegados';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'path_credencial' => FileCast::class,
        'credencial' => 'boolean',
        'pago' => 'boolean',
        'comissoes' => 'array'
    ];

    public string $caminho = 'public/cn/delegados';

    // Status constants
    public const STATUS_REJEITADA = -1;
    public const STATUS_PENDENTE = 0;
    public const STATUS_EM_ANALISE = 1;
    public const STATUS_CONFIRMADA = 2;

    // Oficial constants
    public const OFICIAL_NAO = 0;
    public const OFICIAL_DIACONO = 1;
    public const OFICIAL_PRESBITERO = 2;

    // Status pagamento
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

    public const OFICIAL_LIST = [
        self::OFICIAL_NAO => 'Não',
        self::OFICIAL_DIACONO => 'Diácono',
        self::OFICIAL_PRESBITERO => 'Presbítero'
    ];

    public function getStatusFormatadoAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? '';
    }

    public function getOficialFormatadoAttribute(): string
    {
        return self::OFICIAL_LIST[$this->oficial] ?? 'Não';
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function reuniao(): BelongsTo
    {
        return $this->belongsTo(CongressoReuniao::class, 'reuniao_id');
    }
}

