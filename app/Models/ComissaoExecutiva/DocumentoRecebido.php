<?php

namespace App\Models\ComissaoExecutiva;

use App\Casts\FileCast;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRecebido extends Model
{
    use GenericTrait;

    public string $caminho = 'public/ce/documentos';

    protected $table = 'comissao_executiva_documentos_recebidos';
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

    public const TIPOS_DOCUMENTOS = [
        self::TIPO_DOCUMENTO_SINODAL => 'Documento',
        self::TIPO_CREDENCIAL_SINODAL => 'Credencial'
    ];

    public const STATUS_DOCUMENTO_PENDENTE = 0;
    public const STATUS_DOCUMENTO_RECEBIDO = 1;

    public const STATUS_DOCUMENTO = [
        self::STATUS_DOCUMENTO_PENDENTE => 'Pendente',
        self::STATUS_DOCUMENTO_RECEBIDO => 'Recebido'
    ];

    public function reuniao(): BelongsTo
    {
        return $this->belongsTo(Reuniao::class, 'reuniao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }
}
