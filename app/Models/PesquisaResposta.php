<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class PesquisaResposta extends Model
{
    use GenericTrait;

    protected $table = 'pesquisa_respostas';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'resposta' => 'array'
    ];
}
