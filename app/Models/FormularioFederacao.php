<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormularioFederacao extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $table = 'formularios_federacao_v1';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'perfil' => 'array',
        'deficiencias' => 'array',
        'aci' => 'array',
        'escolaridade' => 'array',
        'estado_civil' => 'array',
        'estrutura' => 'array',
        'programacoes_locais' => 'array',
        'programacoes' => 'array',
    ];

    public function federacao()
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }
}
