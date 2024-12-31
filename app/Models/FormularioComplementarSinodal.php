<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioComplementarSinodal extends Model
{
    use GenericTrait;

    protected $table = 'formulario_complementar_sinodais';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'referencias' => 'array',
        'configuracoes' => 'array',
        'status' => 'boolean'
    ];  
}
