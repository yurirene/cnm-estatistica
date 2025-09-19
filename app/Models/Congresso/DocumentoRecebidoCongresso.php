<?php

namespace App\Models\Congresso;

use App\Casts\FileCast;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRecebidoCongresso extends Model
{
    use GenericTrait;

    public string $caminho = 'public/congresso/documentos';

    protected $table = 'congresso_documentos_recebidos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'path' => FileCast::class,
        'created_at' => 'date',
        'updated_at' => 'date'
    ];

    /**
     * INFORMACOES RETIRADAS DO SIGCE, SE MUDAR AQUI TEM QUE MUDAR LÁ
     */
    public const TIPO_DOCUMENTO_SINODAL = 3;
    public const TIPO_CREDENCIAL_SINODAL = 7;
    public const TIPO_DOCUMENTO_FEDERACAO = 4;
    public const TIPO_CREDENCIAL_FEDERACAO = 8;
    public const TIPO_DOCUMENTO_LOCAL = 5;
    public const TIPO_CREDENCIAL_LOCAL = 9;

    public const TIPOS_DOCUMENTOS = [
        self::TIPO_DOCUMENTO_SINODAL => 'Documento Sinodal',
        self::TIPO_CREDENCIAL_SINODAL => 'Credencial Sinodal',
        self::TIPO_DOCUMENTO_FEDERACAO => 'Documento Federação',
        self::TIPO_CREDENCIAL_FEDERACAO => 'Credencial Federação',
        self::TIPO_DOCUMENTO_LOCAL => 'Documento Local',
        self::TIPO_CREDENCIAL_LOCAL => 'Credencial Local'
    ];

    public const STATUS_DOCUMENTO_PENDENTE = 0;
    public const STATUS_DOCUMENTO_RECEBIDO = 1;

    public const STATUS_DOCUMENTO = [
        self::STATUS_DOCUMENTO_PENDENTE => 'Pendente',
        self::STATUS_DOCUMENTO_RECEBIDO => 'Recebido'
    ];

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
}
