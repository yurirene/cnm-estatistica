<?php

namespace App\Models\CongressoNacional;

use App\Casts\FileCast;
use App\Models\CongressoReuniao;
use App\Models\Federacao;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRecebido extends Model
{
    use GenericTrait;

    public string $caminho = 'public/cn/documentos';

    protected $table = 'congresso_nacional_documentos_recebidos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'path' => FileCast::class,
        'created_at' => 'date',
        'updated_at' => 'date'
    ];

    public const TIPO_DOCUMENTO_FEDERACAO = 1;
    public const TIPO_DOCUMENTO_SINODAL = 2;

    public const TIPOS_DOCUMENTOS = [
        self::TIPO_DOCUMENTO_FEDERACAO => 'Documento da Federação',
        self::TIPO_DOCUMENTO_SINODAL => 'Documento da Sinodal'
    ];

    public const STATUS_DOCUMENTO_PENDENTE = 0;
    public const STATUS_DOCUMENTO_VISTO = 1;
    public const STATUS_DOCUMENTO_RECEBIDO = 2;
    public const STATUS_DOCUMENTO_NAO_RECEBIDO = 3;

    public const STATUS_DOCUMENTO = [
        self::STATUS_DOCUMENTO_PENDENTE => 'Pendente',
        self::STATUS_DOCUMENTO_VISTO => 'Visto',
        self::STATUS_DOCUMENTO_RECEBIDO => 'Recebido',
        self::STATUS_DOCUMENTO_NAO_RECEBIDO => 'Não Recebido'
    ];

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

