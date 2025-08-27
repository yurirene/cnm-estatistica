<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormularioLocal extends Model
{
    use SoftDeletes;

    protected $table = 'formularios_local_v1';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'perfil' => 'array',
        'deficiencias' => 'array',
        'aci' => 'array',
        'escolaridade' => 'array',
        'estado_civil' => 'array',
        'programacoes' => 'array',
        'campo_extra_sinodal' => 'array',
        'campo_extra_federacao' => 'array'
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }
}
