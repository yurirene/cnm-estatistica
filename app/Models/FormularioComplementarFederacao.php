<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioComplementarFederacao extends Model
{
    use GenericTrait;

    protected $table = 'formulario_complementar_federacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'referencias' => 'array',
        'configuracoes' => 'array',
        'status' => 'boolean'
    ];  
}
