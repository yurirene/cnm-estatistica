<?php

namespace App\Models\Pesquisas;

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

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
