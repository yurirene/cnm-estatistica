<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioSinodal extends Model
{
    protected $table = 'formularios_sinodal_v1';
    protected $guarded = ['id', 'created_at', 'updated_at', 'enviado_em'];
    protected $casts = [
        'perfil' => 'array',
        'deficiencias' => 'array',
        'aci' => 'array',
        'escolaridade' => 'array',
        'estado_civil' => 'array',
        'programacoes_federacoes' => 'array',
        'programacoes_locais' => 'array',
        'programacoes' => 'array',
        'discipulado' => 'array',
        'organizacao' => 'array',
        'estrutura' => 'array',
        'enviado_em' => 'datetime'
    ];

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }
}
