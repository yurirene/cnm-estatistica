<?php

namespace App\Models\Estatistica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatisticaGeral extends Model
{
    protected $table = 'estatistica_gerais';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'perfil' => 'array',
        'deficiencias' => 'array',
        'aci' => 'array',
        'escolaridade' => 'array',
        'estado_civil' => 'array',
        'estrutura' => 'array',
        'programacoes_locais' => 'array',
        'programacoes_federacoes' => 'array',
        'programacoes_sinodais' => 'array',
        'abrangencia' => 'array',
    ];
}
