<?php

namespace App\Models\ComissaoExecutiva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoRecebido extends Model
{
    protected $table = 'comissao_executiva_documentos_recebidos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
