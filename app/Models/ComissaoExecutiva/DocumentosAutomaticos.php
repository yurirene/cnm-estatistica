<?php

namespace App\Models\ComissaoExecutiva;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosAutomaticos extends Model
{
    use HasFactory, GenericTrait;

    protected $table = 'comissao_executiva_documentos_automaticos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
        'diretoria' => 'array',
        'relatorio_estatistico' => 'array'
    ];
}
