<?php

namespace App\Models\ComissaoExecutiva;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'comissao_executiva_documentos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
