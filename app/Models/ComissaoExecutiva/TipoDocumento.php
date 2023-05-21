<?php

namespace App\Models\ComissaoExecutiva;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'comissao_executiva_tipos_documentos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
